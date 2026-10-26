# 🚗 Micro-Project Report
## Containerization, Deployment, and Management of a Web Application using Docker and Kubernetes

---

## 📌 Project Overview
- **Project Title:** Smart Car Parking Management System
- **Domain:** Web Development, Cloud Computing, and DevOps
- **Tech Stack:**
  - **Frontend / Application Tier:** PHP 8.2, Apache, HTML5, CSS3, JavaScript
  - **Database Tier:** MariaDB 10.4 (Relational Database)
  - **Containerization:** Docker, Dockerfile, Docker Compose
  - **Orchestration:** Kubernetes (v1.35), Kind (Kubernetes-in-Docker)
  - **Storage:** Kubernetes PersistentVolumeClaims (PVC)
  - **Networking:** Kubernetes Services (`ClusterIP`, `NodePort`)

---

## 🎯 Objectives
1. **Containerize** a multi-tier dynamic web application (Web UI + Database).
2. **Deploy** the containerized application on a Kubernetes cluster with separated namespaces, configuration management, and persistent storage.
3. **Demonstrate Management Capabilities:**
   - Declarative deployments using YAML manifests
   - High availability through multi-replica deployments
   - Horizontal pod scaling (`kubectl scale`)
   - Automated self-healing and pod recovery upon failure
   - Health monitoring via Liveness and Readiness probes
   - Real-time logging and container diagnostics

---

## 🏗️ System Architecture

```
                                  [ User Browser ]
                                         │
                                         ▼
                             http://localhost:30080
                                         │
            ┌────────────────────────────┼────────────────────────────┐
            │                     Kubernetes Node                     │
            │                            │                            │
            │                  NodePort Service: 30080                │
            │                            │                            │
            │               ┌────────────┴────────────┐               │
            │               ▼                         ▼               │
            │     ┌───────────────────┐     ┌───────────────────┐     │
            │     │  Web Pod (Replica)│     │  Web Pod (Replica)│     │
            │     │   PHP 8.2+Apache  │     │   PHP 8.2+Apache  │     │
            │     └─────────┬─────────┘     └─────────┬─────────┘     │
            │               │                         │               │
            │               └────────────┬────────────┘               │
            │                            │ (DNS: db-service:3306)     │
            │                            ▼                            │
            │                ┌───────────────────────┐                │
            │                │   Database Service    │                │
            │                │      (ClusterIP)      │                │
            │                └───────────┬───────────┘                │
            │                            ▼                            │
            │                ┌───────────────────────┐                │
            │                │     Database Pod      │                │
            │                │     (MariaDB 10.4)    │                │
            │                └─────┬───────────┬─────┘                │
            │                      │           │                      │
            │                      ▼           ▼                      │
            │                 [ db-pvc ]   [ ConfigMap ]              │
            │                 (Storage)    (init.sql)                 │
            └─────────────────────────────────────────────────────────┘
```

---

## 1️⃣ Phase 1: Containerization (Docker)

### 1.1 Web Application Dockerfile (`Dockerfile`)
The frontend/backend PHP web tier is packaged using an official Apache image with the required MySQL database drivers:
- **Base image:** `php:8.2-apache`
- **Extensions:** `mysqli`, `pdo`, `pdo_mysql`
- **Security & Permissions:** Non-root ownership (`www-data`) and strict directory permissions.
- **Port:** `80`

### 1.2 Multi-Container Local Testing (`docker-compose.yml`)
Before deploying to Kubernetes, the multi-tier setup was validated locally:
- `car_parking_web`: Exposes web UI on `http://localhost:8080`.
- `car_parking_db`: MariaDB 10.4 with SQL auto-initialization and named volume `db_data`.

```bash
# Build and run with Docker Compose
docker compose up --build -d

# Verify running containers
docker compose ps
```

---

## 2️⃣ Phase 2: Deployment to Kubernetes

### 2.1 Kubernetes Manifests Hierarchy (`k8s/`)

| File | Resource Type | Purpose |
| :--- | :--- | :--- |
| `namespace.yaml` | `Namespace` | Logical isolation under `car-parking` namespace |
| `secret.yaml` | `Secret` | Encrypted storage for sensitive database passwords |
| `configmap.yaml` | `ConfigMap` | Non-sensitive runtime variables (`DB_HOST`, `DB_PORT`, `DB_NAME`) |
| `db-init-configmap.yaml` | `ConfigMap` | Automatic database schema and initial data execution (`init.sql`) |
| `db-pvc.yaml` | `PersistentVolumeClaim` | 1Gi persistent volume claim for MariaDB data storage |
| `db-deployment.yaml` | `Deployment` | Single-replica MariaDB with health probes and volume mounts |
| `db-service.yaml` | `Service` | Internal `ClusterIP` exposing port `3306` to web pods via DNS |
| `web-deployment.yaml` | `Deployment` | Multi-replica web pods with rolling update strategy and HTTP probes |
| `web-service.yaml` | `Service` | `NodePort` exposing web traffic on port `30080` to the host |
| `kustomization.yaml` | `Kustomization` | Unified deployment manifest enabling one-command setup |

### 2.2 Deployment Execution
```bash
# Apply all manifests in one command
kubectl apply -k k8s/

# Verify resource provisioning
kubectl get all -n car-parking
kubectl get pvc -n car-parking
```

---

## 3️⃣ Phase 3: Demonstration of Application Management

### 3.1 Verification and Access
- **Command:** `kubectl get pods -n car-parking -o wide`
- **Accessing the App:**
  - Direct via NodePort: [http://localhost:30080](http://localhost:30080)
  - Or via Port-Forwarding:
    ```bash
    kubectl port-forward svc/web-service 8080:80 -n car-parking
    ```

### 3.2 Horizontal Scaling Demonstration
Kubernetes allows dynamic scaling of the application to handle changes in traffic demands:
```bash
# Scale up from 2 to 4 replicas
kubectl scale deployment web-deployment --replicas=4 -n car-parking

# Verify new pods have been scheduled and are Running
kubectl get pods -n car-parking -l app=car-parking-web

# Scale back down to 2 replicas
kubectl scale deployment web-deployment --replicas=2 -n car-parking
```

### 3.3 Self-Healing (Fault Tolerance) Demonstration
If a container or pod crashes or is terminated, Kubernetes automatically recreates it to match the desired state declared in the deployment:
```bash
# Identify a running web pod
kubectl get pods -n car-parking -l app=car-parking-web

# Simulate failure by deleting a pod
kubectl delete pod <POD_NAME> -n car-parking

# Notice Kubernetes immediately launched a replacement pod within seconds
kubectl get pods -n car-parking -l app=car-parking-web
```

### 3.4 Rolling Updates Demonstration
Zero-downtime rolling updates allow deploying new code without service disruption:
```bash
# View rollout status
kubectl rollout status deployment/web-deployment -n car-parking

# Trigger a restart or image update
kubectl rollout restart deployment/web-deployment -n car-parking

# Check deployment rollout history
kubectl rollout history deployment/web-deployment -n car-parking
```

### 3.5 Health Probes Monitoring
Both web and database pods include **Liveness** and **Readiness** probes:
- **Web Readiness Probe:** HTTP `GET /index.php` on port 80 ensures the container only receives traffic when Apache is healthy.
- **Database Readiness Probe:** `mysqladmin ping` verifies MariaDB is ready to accept connections before routing app queries.

```bash
# Inspect probe events and status
kubectl describe pod -l app=car-parking-web -n car-parking
```

### 3.6 Log Aggregation and Diagnostics
```bash
# Stream live logs from all web replicas
kubectl logs -l app=car-parking-web -n car-parking --tail=50 -f

# Inspect database container logs
kubectl logs -l app=car-parking-db -n car-parking --tail=50
```

---

## 4️⃣ Viva & Evaluation Q&A Cheat Sheet

| Question | Answer Summary |
| :--- | :--- |
| **Why use Docker?** | To package code, runtime, libraries, and configurations into an immutable container that runs identically on any environment. |
| **Why use Kubernetes instead of just Docker Compose?** | Docker Compose runs on a single host. Kubernetes orchestrates multi-host clusters, handles automatic scaling, self-healing, rolling updates, and service discovery. |
| **What is the difference between ClusterIP and NodePort?** | `ClusterIP` exposes a service only inside the cluster (used for database security). `NodePort` exposes a service on a static port on each cluster node for external access. |
| **How does data persist in Kubernetes?** | Through `PersistentVolumeClaim` (PVC) bound to a `PersistentVolume` (PV). When a MariaDB pod restarts or moves nodes, the storage volume remains intact. |
| **What happens if a pod crashes?** | The ReplicaSet controller detects the difference between desired state (e.g. 2 replicas) and current state (1 replica) and immediately spawns a new pod. |

---

## 5️⃣ Summary Table

| Requirement | Implementation | Proof / Verification |
| :--- | :--- | :--- |
| **Web Application** | Car Parking Management System | HTML/CSS/PHP + MariaDB database |
| **Containerization** | `Dockerfile` + `docker-compose.yml` | Images: `car-parking-web:v1`, `mariadb:10.4` |
| **Deployment** | Kubernetes Manifests (`k8s/`) | `Namespace`, `ConfigMap`, `Secret`, `PVC`, `Deployments`, `Services` |
| **Management** | Scaling, Self-healing, Probes, Rollouts | `kubectl scale`, `kubectl delete pod`, Liveness/Readiness probes |
