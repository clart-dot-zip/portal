# Git Management SSH User Setup Guide

This guide walks you through creating a dedicated SSH user on a Pterodactyl node so the Portal can execute docker-based git commands safely. The Portal application and the SSH entry point run on the same server in this scenario. The steps assume you have privileged access (root or sudo) and that the node uses a Linux distribution such as Ubuntu or Debian.

## 1. Plan Credentials and Access

- Choose a username that reflects the portal usage, e.g. `portal_git`.
- Decide where the SSH private key will live on the Portal host (matches `PTERODACTYL_DEFAULT_SSH_KEY_PATH`).
- Confirm which Pterodactyl containers the user must access (usually the Garry's Mod server container).

## 2. Create the System User on the Node

```bash
sudo adduser --system --shell /bin/bash --group --home /opt/portal_git portal_git
```

This command creates:
- A system account `portal_git`.
- A dedicated home directory at `/opt/portal_git`.
- A matching group `portal_git`.

> If your distribution does not support `--system`, use `sudo adduser --disabled-password portal_git` and manually adjust the home directory.

## 3. Provision SSH Directory and Permissions

```bash
sudo mkdir -p /opt/portal_git/.ssh
sudo chown portal_git:portal_git /opt/portal_git/.ssh
sudo chmod 700 /opt/portal_git/.ssh
```

These commands prepare the `.ssh` directory with secure ownership and permissions required for OpenSSH.

## 4. Generate an SSH Key Pair on the Server

Because the Portal and SSH service share the same host, generate the key pair directly on that machine:

```bash
ssh-keygen -t ed25519 -f /home/portal/.ssh/pterodactyl -C "portal git automation"
```

- When prompted, set a passphrase or leave blank if the process runs unattended.
- The private key path must match `PTERODACTYL_DEFAULT_SSH_KEY_PATH` (or the per-server override).

## 5. Install the Public Key Locally

Append the freshly generated public key to the dedicated user’s `authorized_keys` file:

```bash
sudo tee -a /opt/portal_git/.ssh/authorized_keys > /dev/null <<'EOF'
ssh-ed25519 AAAAC3... portal git automation
EOF
sudo chown portal_git:portal_git /opt/portal_git/.ssh/authorized_keys
sudo chmod 600 /opt/portal_git/.ssh/authorized_keys
```

> Replace the `ssh-ed25519 AAAAC3...` placeholder with the actual public key string (use `cat /home/portal/.ssh/pterodactyl.pub` to display it).

## 6. Restrict Shell Access (Optional but Recommended)

Limit the user to docker commands by configuring a forced command or using `sudo` rules.

### Option A: Forced Command Wrapper

1. Create a wrapper script:
   ```bash
   sudo tee /usr/local/bin/portal-docker-wrapper > /dev/null <<'EOF'
   #!/bin/bash
   exec /usr/bin/docker "$@"
   EOF
   sudo chmod 750 /usr/local/bin/portal-docker-wrapper
   sudo chown root:portal_git /usr/local/bin/portal-docker-wrapper
   ```
2. Update the `authorized_keys` entry to force the wrapper:
   ```text
   command="/usr/local/bin/portal-docker-wrapper" ssh-ed25519 AAAAC3... portal git automation
   ```

### Option B: Sudoers Rule

If the user must run `docker exec` with sudo:

```bash
echo 'portal_git ALL=(root) NOPASSWD: /usr/bin/docker exec *' | sudo tee /etc/sudoers.d/portal_git
sudo chmod 440 /etc/sudoers.d/portal_git
```

Then prepend `sudo` to docker commands in the Portal configuration (`docker exec` → `sudo docker exec`).

## 7. Configure sshd for Key Authentication (Recommended)

Ensure the SSH daemon is explicitly configured to accept keys for the dedicated user. Create a drop-in file so upgrades do not overwrite your changes:

```bash
sudo tee /etc/ssh/sshd_config.d/portal_git.conf > /dev/null <<'EOF'
Match User portal_git
   PubkeyAuthentication yes
   PasswordAuthentication no
   KbdInteractiveAuthentication no
   AuthorizedKeysFile %h/.ssh/authorized_keys
EOF
sudo sshd -t
sudo systemctl restart sshd
```

The `sshd -t` command validates syntax before restarting. After the reload, the `portal_git` account will authenticate exclusively with a key, preventing password prompts during automation.

## 8. Allow SSH Through Firewall (If Applicable)

Ensure port 22 (or the custom SSH port) is open:

```bash
sudo ufw allow OpenSSH
# or for custom port
sudo ufw allow 2222/tcp
```

## 9. Test SSH Connectivity

From the same server:

```bash
ssh -i /home/portal/.ssh/pterodactyl portal_git@<NODE_FQDN> -p <SSH_PORT> "docker ps"
```

- Replace `<NODE_FQDN>` with the node’s hostname or IP (matching `ssh_host`). You can use `localhost` if SSH is bound locally.
- Replace `<SSH_PORT>` with the node’s SSH port.
- Successful output should list running containers without password prompts.

## 10. Configure Portal Environment Variables

Update `.env` (or per-server overrides) with the SSH settings:

```text
PTERODACTYL_DEFAULT_SSH_USER=portal_git
PTERODACTYL_DEFAULT_SSH_PORT=22
PTERODACTYL_DEFAULT_SSH_KEY_PATH=/home/portal/.ssh/pterodactyl
```

Run `php artisan config:clear` or restart the application to apply the changes.

## 11. Add the Server in the Portal UI

1. Open **Git Management → Add Server**.
2. Select the target server from the Pterodactyl list.
3. Confirm the repository path, remote, branch, and SSH details match the user you created.
4. Save and then use the quick commands (fetch/pull/checkout/status) to verify git access inside the container.

## 12. Operational Tips

- Rotate keys periodically by generating a new pair and updating `authorized_keys`.
- Monitor `/opt/portal_git/.ssh/authorized_keys` for unauthorized changes.
- Keep the wrapper or sudo rules as restrictive as possible to prevent misuse.
- Log git operations in the Portal to audit activity (`git_operation_logs` table).

Following these steps gives the Portal a secure, dedicated channel to execute docker-based git commands on Pterodactyl nodes without sharing root credentials.
