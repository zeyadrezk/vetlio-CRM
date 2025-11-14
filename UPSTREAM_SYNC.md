# Upstream Sync Setup Guide

This guide explains how to keep your fork synchronized with the upstream (main) repository.

---

## 🔄 Automatic Daily Sync

### GitHub Actions Workflow

A GitHub Actions workflow has been configured to automatically sync your fork with the upstream repository **daily at 2:00 AM UTC**.

**Workflow File**: `.github/workflows/sync-upstream.yml`

### Setup Instructions

1. **Configure the Upstream Repository**

   Edit `.github/workflows/sync-upstream.yml` and replace the placeholders:

   ```yaml
   # Line 26-27
   git remote add upstream https://github.com/UPSTREAM_OWNER/UPSTREAM_REPO.git
   ```

   Replace with the actual upstream repository URL. For example:
   ```yaml
   git remote add upstream https://github.com/original-owner/vetlio-CRM.git
   ```

2. **Enable GitHub Actions**

   - Go to your repository on GitHub
   - Navigate to **Settings** → **Actions** → **General**
   - Under "Actions permissions", select **"Allow all actions and reusable workflows"**
   - Click **Save**

3. **Verify Workflow**

   - Go to **Actions** tab in your repository
   - You should see "Sync Fork with Upstream" workflow listed
   - Click **Run workflow** to manually trigger a test sync

### How It Works

The workflow:
1. ✅ Runs daily at 2:00 AM UTC
2. ✅ Can be triggered manually via GitHub Actions UI
3. ✅ Fetches latest changes from upstream
4. ✅ Merges upstream main/master into your fork's main/master
5. ✅ Pushes updates to your fork
6. ✅ Creates a sync report in the workflow summary
7. ⚠️ Notifies you if conflicts require manual resolution

---

## 🛠️ Manual Sync (Alternative Method)

If you prefer to sync manually or if the automatic sync fails:

### Initial Setup

1. **Add upstream remote** (one-time setup):
   ```bash
   git remote add upstream <UPSTREAM_REPOSITORY_URL>
   ```

   Example:
   ```bash
   git remote add upstream https://github.com/original-owner/vetlio-CRM.git
   ```

2. **Verify remotes**:
   ```bash
   git remote -v
   ```

   You should see:
   ```
   origin    https://github.com/YOUR_USERNAME/vetlio-CRM.git (fetch)
   origin    https://github.com/YOUR_USERNAME/vetlio-CRM.git (push)
   upstream  https://github.com/UPSTREAM_OWNER/vetlio-CRM.git (fetch)
   upstream  https://github.com/UPSTREAM_OWNER/vetlio-CRM.git (push)
   ```

### Syncing Process

1. **Fetch upstream changes**:
   ```bash
   git fetch upstream
   ```

2. **Checkout your main branch**:
   ```bash
   git checkout main
   # or
   git checkout master
   ```

3. **Merge upstream changes**:
   ```bash
   git merge upstream/main
   # or
   git merge upstream/master
   ```

4. **Push to your fork**:
   ```bash
   git push origin main
   # or
   git push origin master
   ```

### Handling Merge Conflicts

If you encounter conflicts during merge:

1. **View conflicted files**:
   ```bash
   git status
   ```

2. **Resolve conflicts** in each file:
   - Open the file in your editor
   - Look for conflict markers: `<<<<<<<`, `=======`, `>>>>>>>`
   - Manually resolve the conflicts
   - Save the file

3. **Mark as resolved**:
   ```bash
   git add <conflicted-file>
   ```

4. **Complete the merge**:
   ```bash
   git commit -m "Merge upstream changes and resolve conflicts"
   ```

5. **Push to your fork**:
   ```bash
   git push origin main
   ```

---

## 📅 Sync Schedule Customization

To change the automatic sync schedule, edit `.github/workflows/sync-upstream.yml`:

```yaml
on:
  schedule:
    - cron: '0 2 * * *'  # Current: Daily at 2 AM UTC
```

**Common Schedules:**
- Every 12 hours: `'0 */12 * * *'`
- Twice daily (2 AM and 2 PM): `'0 2,14 * * *'`
- Weekly on Monday at 2 AM: `'0 2 * * 1'`
- Every 6 hours: `'0 */6 * * *'`

**Cron Format**: `minute hour day month weekday`

Use [crontab.guru](https://crontab.guru/) to generate custom schedules.

---

## 🔍 Monitoring Sync Status

### Via GitHub Actions

1. Go to your repository on GitHub
2. Click the **Actions** tab
3. View recent "Sync Fork with Upstream" workflow runs
4. Click on a run to see detailed logs and sync report

### Via Git Log

Check recent upstream commits merged:

```bash
git log --oneline --graph --all -20
```

Check when upstream was last fetched:

```bash
git remote show upstream
```

---

## ⚠️ Important Considerations

### Translation Work Protection

When syncing from upstream:

1. **Your translation files are safe** - They exist only in your fork
2. **Custom modifications** in translation files won't be overwritten
3. **New upstream features** may need translation after sync
4. **Always review** changes after sync to identify new strings

### Recommended Workflow

1. **Before starting translation work**:
   ```bash
   git fetch upstream
   git merge upstream/main
   git push origin main
   ```

2. **Create feature branch** for translation work:
   ```bash
   git checkout -b translate/resource-name
   ```

3. **Work on translations** in the feature branch

4. **Regularly sync main branch** (but not your feature branch)

5. **Merge main into feature branch** when needed:
   ```bash
   git checkout translate/resource-name
   git merge main
   ```

### Conflict Prevention

To minimize conflicts:

1. **Don't modify upstream files directly** - Only add translation files
2. **Use separate branches** for translation work
3. **Sync frequently** to stay up-to-date
4. **Review changelog** of upstream project before merging

---

## 🚀 Quick Reference

### One-Time Setup
```bash
# Add upstream remote
git remote add upstream <UPSTREAM_URL>

# Verify
git remote -v
```

### Daily Sync (Manual)
```bash
# Fetch and merge
git fetch upstream
git checkout main
git merge upstream/main
git push origin main
```

### Check Sync Status
```bash
# View remotes
git remote -v

# Check upstream
git fetch upstream
git log HEAD..upstream/main --oneline

# Compare branches
git diff main upstream/main
```

---

## 📚 Additional Resources

- [GitHub: Syncing a Fork](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/syncing-a-fork)
- [GitHub Actions: Scheduled Events](https://docs.github.com/en/actions/using-workflows/events-that-trigger-workflows#schedule)
- [Git: Working with Remotes](https://git-scm.com/book/en/v2/Git-Basics-Working-with-Remotes)

---

**Last Updated**: 2025-11-14
**Automation**: Daily at 2:00 AM UTC
**Method**: GitHub Actions + Manual Option
