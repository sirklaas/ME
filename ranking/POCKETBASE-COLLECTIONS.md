# PocketBase Collections Setup

## Collection 1: `votes`

**Purpose:** Store individual votes from voters

### Fields:
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `session_id` | Text | Yes | Unique session identifier |
| `round` | Number | Yes | Round number (1-3) |
| `voter_id` | Text | Yes | Device fingerprint (anonymous) |
| `image_id` | Number | Yes | The image voted for (1-4) |

### Indexes:
- **Name:** `unique_vote`
- **Fields:** `session_id`, `round`, `voter_id`
- **Unique:** ✅ Yes

### API Rules:
```javascript
listRule:   "" // Allow public
viewRule:   "" // Allow public
createRule: "" // Allow public
updateRule: null // Deny all
deleteRule: null // Deny all
```

---

## Collection 2: `voting_session`

**Purpose:** Store session configuration (images, players)

### Fields:
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `session_id` | Text | Yes | Unique session identifier |
| `images` | JSON | Yes | Array of 4 image objects: `[{id, url, title}]` |
| `players` | JSON | Yes | Array of player names: `["Name 1", "Name 2"]` |

### Indexes:
- **Name:** `unique_session`
- **Fields:** `session_id`
- **Unique:** ✅ Yes

### API Rules:
```javascript
listRule:   "" // Allow public
viewRule:   "" // Allow public
createRule: "" // Allow public
updateRule: "" // Allow public (to update config)
deleteRule: "" // Allow public (to clear old sessions)
```

---

## Setup Instructions

### 1. Access PocketBase Admin
Go to: **https://ranking.pinkmilk.eu/_/**

### 2. Create `votes` Collection

1. Click "New Collection"
2. Name: `votes`
3. Type: Base Collection
4. Add fields:
   - `session_id` → Text (Required)
   - `round` → Number (Required)
   - `voter_id` → Text (Required)
   - `image_id` → Number (Required)
5. Create Index:
   - Click "Indexes" tab
   - Add index: `session_id`, `round`, `voter_id`
   - Check "Unique"
6. Set API Rules (see above)
7. Save

### 3. Create `voting_session` Collection

1. Click "New Collection"
2. Name: `voting_session`
3. Type: Base Collection
4. Add fields:
   - `session_id` → Text (Required)
   - `images` → JSON (Required)
   - `players` → JSON (Required)
5. Create Index:
   - Click "Indexes" tab
   - Add index: `session_id`
   - Check "Unique"
6. Set API Rules (see above)
7. Save

---

## Example Data

### `votes` Record:
```json
{
  "id": "abc123xyz",
  "session_id": "session_1731359400000_abc123xyz",
  "round": 1,
  "voter_id": "dGVzdF91c2VyX2ZpbmdlcnByaW50",
  "image_id": 2,
  "created": "2025-11-12T15:30:00.000Z"
}
```

### `voting_session` Record:
```json
{
  "id": "config123",
  "session_id": "session_1731359400000_abc123xyz",
  "images": [
    {"id": 1, "url": "https://...", "title": "Option 1"},
    {"id": 2, "url": "https://...", "title": "Option 2"},
    {"id": 3, "url": "https://...", "title": "Option 3"},
    {"id": 4, "url": "https://...", "title": "Option 4"}
  ],
  "players": ["Alice", "Bob", "Charlie"],
  "created": "2025-11-12T15:00:00.000Z",
  "updated": "2025-11-12T15:30:00.000Z"
}
```

---

## Benefits of PocketBase Storage

✅ **Persistent** - Survives browser refresh  
✅ **Shareable** - Same config across all devices  
✅ **Centralized** - One source of truth  
✅ **Backup** - Data stored on server  
✅ **Queryable** - Can view/export all sessions  

---

## Migration from localStorage

The app now:
1. **Saves** to PocketBase first
2. **Falls back** to localStorage as backup
3. **Loads** from PocketBase if available
4. **Falls back** to localStorage if PocketBase fails

This ensures backward compatibility!
