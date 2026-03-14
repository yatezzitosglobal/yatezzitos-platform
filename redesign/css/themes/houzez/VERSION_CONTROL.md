# Houzez Theme - Version Control Guide

This document explains the Git-based version control system for the Houzez WordPress theme, including branching strategy, tagging conventions, and release workflows.

## Table of Contents

- [Overview](#overview)
- [Branch Strategy](#branch-strategy)
- [Tagging Convention](#tagging-convention)
- [Release Workflow](#release-workflow)
- [Common Commands](#common-commands)
- [Automated Release Script](#automated-release-script)
- [Accessing Old Versions](#accessing-old-versions)
- [Best Practices](#best-practices)

---

## Overview

The Houzez theme uses a dual-branch strategy with Git tags to manage version releases. This allows you to:

- Maintain separate development and production codebases
- Create and track multiple versions
- Easily download or checkout any previous version
- Generate distribution archives for specific versions

**Current Setup:**
- Repository: `https://github.com/waqasriaz/Houzezwp.git`
- Primary branches: `main` and `develop`
- Tagging convention: `v4.2.5` (stable) and `v4.2.5-dev` (development)

---

## Branch Strategy

### `develop` Branch
- **Purpose:** Active development and feature integration
- **Tag Format:** `v4.2.6-dev`, `v4.2.7-rc1` (release candidates)
- **Stability:** May contain bugs, experimental features
- **Usage:** Day-to-day development work

### `main` Branch
- **Purpose:** Stable, production-ready releases
- **Tag Format:** `v4.2.6`, `v4.2.7`
- **Stability:** Fully tested, ready for distribution
- **Usage:** Official releases only

### Workflow Pattern
```
develop → (test & verify) → main
```

---

## Tagging Convention

Git tags mark specific commits as release points. Tags are **permanent** and **immutable**.

### Development Tags
**Format:** `v{MAJOR}.{MINOR}.{PATCH}-{SUFFIX}`

Examples:
- `v4.2.6-dev` - Development version
- `v4.2.6-rc1` - Release candidate 1
- `v4.2.6-rc2` - Release candidate 2
- `v4.2.6-beta` - Beta release

**When to use:**
- Feature complete but needs testing
- Pre-release versions for QA
- Internal distribution

### Production Tags
**Format:** `v{MAJOR}.{MINOR}.{PATCH}`

Examples:
- `v4.2.5` - Current stable release
- `v4.2.6` - Next stable release
- `v4.3.0` - Minor version bump
- `v5.0.0` - Major version bump

**When to use:**
- Production-ready releases
- Public distribution
- WordPress.org submissions

### Version Numbering

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** (v5.0.0): Breaking changes, major redesigns
- **MINOR** (v4.3.0): New features, backwards compatible
- **PATCH** (v4.2.6): Bug fixes, small improvements

---

## Release Workflow

### Option 1: Manual Release Process

#### Step 1: Development Release (on `develop` branch)

```bash
# Ensure you're on develop branch
git checkout develop

# Make your changes
# ... edit files ...

# Update version in style.css to 4.2.6
# Then commit changes
git add .
git commit -m "Development release v4.2.6" --author="Waqas Riaz <waqas@example.com>"

# Create development tag
git tag -a v4.2.6-dev -m "Development release v4.2.6"

# Push to remote
git push origin develop
git push origin v4.2.6-dev
```

#### Step 2: Stable Release (on `main` branch)

```bash
# Switch to main branch
git checkout main

# Merge from develop (when ready for production)
git merge develop

# Create stable tag
git tag -a v4.2.6 -m "Stable release v4.2.6"

# Push to remote
git push origin main
git push origin v4.2.6
```

### Option 2: Automated Release Script

Use the included `release.sh` script for streamlined releases:

#### Development Release
```bash
./release.sh -v 4.2.6 -t dev -p
```

#### Release Candidate
```bash
./release.sh -v 4.2.6 -t rc -p
```

#### Stable Release
```bash
./release.sh -v 4.2.6 -t stable -p
```

**Script Features:**
- Automatically updates `style.css` version
- Creates properly formatted commits (authored by Waqas Riaz)
- Generates annotated Git tags
- Optional automatic push to remote
- Interactive confirmation prompts
- Color-coded output

---

## Common Commands

### Viewing Tags

```bash
# List all tags
git tag -l

# List only stable releases
git tag -l "v*" | grep -v "-"

# List only development releases
git tag -l "v*-dev"

# Show tag details
git show v4.2.5
```

### Checking Out Old Versions

```bash
# Checkout a specific version (detached HEAD)
git checkout v4.2.5

# Create a branch from a tag
git checkout -b hotfix-4.2.5 v4.2.5

# Return to develop branch
git checkout develop
```

### Creating Archives

```bash
# Create a zip archive of a specific version
git archive -o houzez-v4.2.5.zip v4.2.5

# Create a tar.gz archive
git archive -o houzez-v4.2.5.tar.gz v4.2.5

# Archive with prefix directory
git archive --prefix=houzez/ -o houzez-v4.2.5.zip v4.2.5
```

### Comparing Versions

```bash
# See changes between two versions
git diff v4.2.4 v4.2.5

# See changed files only
git diff --name-only v4.2.4 v4.2.5

# See commit log between versions
git log v4.2.4..v4.2.5 --oneline
```

### Deleting Tags

```bash
# Delete local tag
git tag -d v4.2.5-dev

# Delete remote tag
git push origin --delete v4.2.5-dev

# Delete both local and remote
git tag -d v4.2.5-dev && git push origin --delete v4.2.5-dev
```

---

## Automated Release Script

The `release.sh` script automates the entire release process.

### Script Usage

```bash
./release.sh [OPTIONS]
```

### Options

| Option | Description | Example |
|--------|-------------|---------|
| `-v, --version` | Version number (required) | `-v 4.2.6` |
| `-t, --type` | Release type: dev, rc, stable | `-t stable` |
| `-m, --message` | Custom tag message | `-m "Bug fix release"` |
| `-p, --push` | Push to remote immediately | `-p` |
| `-h, --help` | Display help message | `-h` |

### Examples

```bash
# Development release without push
./release.sh -v 4.2.6 -t dev

# Stable release with automatic push
./release.sh -v 4.2.6 -t stable -p

# Release candidate with custom message
./release.sh -v 4.2.7 -t rc -m "Feature X release candidate" -p

# Quick stable release
./release.sh -v 4.2.8 -t stable -p
```

### What the Script Does

1. ✓ Validates version format and release type
2. ✓ Displays current version and changes
3. ✓ Checks for uncommitted changes
4. ✓ Updates version in `style.css`
5. ✓ Creates properly formatted commit
6. ✓ Generates annotated Git tag
7. ✓ Optionally pushes to remote
8. ✓ Provides next steps guidance

---

## Accessing Old Versions

### Scenario 1: Download a Specific Version

```bash
# Clone the repository
git clone https://github.com/waqasriaz/Houzezwp.git
cd Houzezwp/wp-content/themes/houzez

# Checkout the desired version
git checkout v4.2.5

# Create a distribution zip
git archive -o ~/houzez-v4.2.5.zip v4.2.5
```

### Scenario 2: View Old Code

```bash
# Temporarily checkout an old version
git checkout v4.2.4

# Look at specific file from old version
git show v4.2.4:style.css

# Return to current development
git checkout develop
```

### Scenario 3: Apply Hotfix to Old Version

```bash
# Create a branch from old version
git checkout -b hotfix-4.2.4 v4.2.4

# Make your fixes
# ... edit files ...

# Commit and tag the hotfix
git commit -m "Hotfix for v4.2.4"
git tag -a v4.2.4.1 -m "Hotfix release"

# Push the hotfix
git push origin hotfix-4.2.4
git push origin v4.2.4.1
```

### Scenario 4: Create GitHub Release

After pushing tags, create releases on GitHub:

1. Go to: `https://github.com/waqasriaz/Houzezwp/releases`
2. Click "Draft a new release"
3. Select your tag (e.g., `v4.2.6`)
4. Add release notes
5. Upload the theme zip file
6. Publish release

---

## Best Practices

### 1. Always Tag Releases
Every production release should have a corresponding Git tag.

### 2. Use Annotated Tags
Always use `git tag -a` (annotated) instead of lightweight tags. Annotated tags store metadata.

```bash
# Good (annotated)
git tag -a v4.2.6 -m "Stable release v4.2.6"

# Avoid (lightweight)
git tag v4.2.6
```

### 3. Keep Branches Clean
- Commit regularly on `develop`
- Only merge to `main` when fully tested
- Delete feature branches after merging

### 4. Semantic Versioning
Follow semantic versioning rules:
- Bug fix: Increment PATCH (4.2.5 → 4.2.6)
- New feature: Increment MINOR (4.2.6 → 4.3.0)
- Breaking change: Increment MAJOR (4.3.0 → 5.0.0)

### 5. Document Changes
Maintain a CHANGELOG.md file documenting changes between versions:

```markdown
## [4.2.6] - 2025-11-05
### Added
- New property search filter
- Cloudflare Turnstile captcha support

### Fixed
- Agent reviews pagination
- Lightbox display issues
```

### 6. Test Before Tagging
- Always test thoroughly before creating stable tags
- Use `-dev` or `-rc` tags for pre-release testing
- Never move or delete stable release tags

### 7. Consistent Authorship
Per CLAUDE.md guidelines, all commits must be authored by Waqas Riaz:

```bash
git commit -m "Message" --author="Waqas Riaz <waqas@example.com>"
```

The `release.sh` script handles this automatically.

---

## Quick Reference

### Starting a New Version

```bash
# On develop branch
./release.sh -v 4.2.7 -t dev

# Test and iterate
# When stable, merge to main
git checkout main
git merge develop

# Create stable release
./release.sh -v 4.2.7 -t stable -p
```

### Viewing Version History

```bash
# All versions
git tag -l

# Recent commits
git log --oneline --graph --all

# Changes since last release
git log v4.2.5..HEAD --oneline
```

### Distribution Package

```bash
# Current version
git archive -o houzez-latest.zip HEAD

# Specific version
git archive -o houzez-v4.2.6.zip v4.2.6
```

---

## Troubleshooting

### Problem: Tag Already Exists

```bash
# Error: tag 'v4.2.6' already exists

# Solution: Delete and recreate
git tag -d v4.2.6
git push origin --delete v4.2.6
git tag -a v4.2.6 -m "Stable release v4.2.6"
git push origin v4.2.6
```

### Problem: Wrong Branch Tagged

```bash
# If you tagged the wrong branch
git tag -d v4.2.6
git checkout correct-branch
git tag -a v4.2.6 -m "Stable release v4.2.6"
```

### Problem: Forgot to Push Tags

```bash
# Push all tags at once
git push origin --tags

# Or push specific tag
git push origin v4.2.6
```

---

## Additional Resources

- [Git Tagging Documentation](https://git-scm.com/book/en/v2/Git-Basics-Tagging)
- [Semantic Versioning](https://semver.org/)
- [GitHub Releases](https://docs.github.com/en/repositories/releasing-projects-on-github)
- [WordPress Theme Versioning](https://developer.wordpress.org/themes/basics/main-stylesheet-style-css/)

---

## Support

For questions about version control or release process:
- Contact: Waqas Riaz
- Repository: https://github.com/waqasriaz/Houzezwp

---

**Last Updated:** 2025-11-05
**Current Version:** 4.2.5
**Theme:** Houzez v4.x
