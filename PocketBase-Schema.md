# Simplified PocketBase Schema for The Masked Employee Gameshow

## Collection: `submissions`

This collection stores all player submissions for the gameshow using a flexible JSON-based approach.

### Fields Structure

| Field Name | Type | Required | Description |
|------------|------|----------|-------------|
| `id` | Text | Auto | Auto-generated unique identifier |
| `created` | DateTime | Auto | Auto-generated creation timestamp |
| `updated` | DateTime | Auto | Auto-generated update timestamp |
| `gamename` | Text | Yes | Name of the gameshow/event (e.g., "The Masked Employee") |
| `nameplayer` | Text | Yes | Full name of the participant |
| `chapter01` | JSON | Yes | Introductie & Basisinformatie (Questions 1-5) |
| `chapter02` | JSON | Yes | Masked Identity (Questions 6-10) |
| `chapter03` | JSON | Yes | Persoonlijke Eigenschappen (Questions 11-16) |
| `chapter04` | JSON | Yes | Verborgen Talenten (Questions 17-21) |
| `chapter05` | JSON | Yes | Jeugd & Verleden (Questions 22-26) |
| `chapter06` | JSON | Yes | Fantasie & Dromen (Questions 27-31) |
| `chapter07` | JSON | Yes | Eigenaardigheden (Questions 32-36) |
| `chapter08` | JSON | Yes | Onverwachte Voorkeuren (Questions 37-40) |

### JSON Structure for Chapter Fields

Each chapter field stores answers for that specific section as a JSON object:

**Example chapter01 (Questions 1-5):**
```json
{
  "1": 0,  // Multiple choice answer (index)
  "2": 1,  // Multiple choice answer (index)
  "3": 2,  // Multiple choice answer (index)
  "4": 1,  // Multiple choice answer (index)
  "5": 0   // Multiple choice answer (index)
}
```

**Example chapter03 (Questions 11-16 with text answers):**
```json
{
  "11": "I would choose teleportation because it would save so much time...",
  "12": "I'm afraid of butterflies - I know it sounds silly but...",
  "13": "Einstein, because I'd love to discuss the nature of time...",
  "14": "The concept of parallel universes and quantum mechanics...",
  "15": "The Procrastinator's Guide to Success",
  "16": "I once snuck out to see a concert when I was 16..."
}
```

#### Benefits of Chapter-Based Approach:
- **Organized**: Answers grouped by logical sections
- **Flexible**: Easy to analyze specific topics
- **Maintainable**: Can query individual chapters
- **Scalable**: Add/remove chapters without affecting others
- **Structured**: Better data organization for analysis

### Additional Metadata Fields

| Field Name | Type | Description |
|------------|------|-------------|
| `ip_address` | Text | IP address for tracking (optional) |
| `user_agent` | Text | Browser information (optional) |
| `completion_time_minutes` | Number | Time taken to complete form |
| `notes` | Text | Admin notes about submission |
| `reviewed_by` | Text | Admin who reviewed submission |
| `review_date` | DateTime | When submission was reviewed |

## Collection Rules & Permissions

### Create Rules
- Allow anyone to create submissions (public form)
- Validate required fields
- Ensure player_name is at least 2 characters

### Read Rules
- Only authenticated admins can read submissions
- Participants cannot see other submissions

### Update Rules
- Only admins can update submissions
- Add review status and notes

### Delete Rules
- Only super admins can delete submissions

## Indexes for Performance

Create indexes on frequently queried fields:
- `player_name` (for searching participants)
- `submission_date` (for date-based queries)
- `status` (for filtering by completion status)
- `q1_gender`, `q2_relationship`, `q3_children`, `q4_age_category` (for demographic analysis)

## Easy PocketBase Setup

### Step 1: Create Collection in PocketBase Admin
1. Open PocketBase Admin UI
2. Go to "Collections" → "New Collection"
3. Name: `submissions`
4. Type: `Base`

### Step 2: Add Fields
Add these fields one by one:

| Field Name | Type | Options |
|------------|------|---------|
| `gamename` | Text | Required |
| `nameplayer` | Text | Required |
| `answers` | JSON | Required |
| `submission_date` | DateTime | Required |
| `total_questions` | Number | Required |
| `status` | Select | Options: `completed`, `partial`, `reviewed` |

### Step 3: Set Permissions
- **Create**: `@request.data.gamename != "" && @request.data.nameplayer != ""`
- **View**: `@request.auth.id != ""` (admin only)
- **Update**: `@request.auth.id != ""` (admin only)
- **Delete**: `@request.auth.id != ""` (admin only)

## API Integration Code

```javascript
// Simple PocketBase integration
async saveToPocketBase(data) {
    const submissionData = {
        gamename: "The Masked Employee",
        nameplayer: data.playerName,
        answers: data.answers, // All answers as JSON!
        submission_date: data.timestamp,
        total_questions: data.totalQuestions,
        status: 'completed'
    };
    
    const pb = new PocketBase('YOUR_POCKETBASE_URL');
    const record = await pb.collection('submissions').create(submissionData);
    return record;
}
```

## Data Export & Analysis

With this structure, you can easily:
- Export all submissions to CSV/Excel
- Analyze demographic patterns
- Create reports by question category
- Generate statistics for the gameshow
- Search for specific participants or answers

## Security Considerations

1. **Data Privacy**: Ensure GDPR compliance for personal data
2. **Rate Limiting**: Prevent spam submissions
3. **Validation**: Server-side validation of all inputs
4. **Backup**: Regular backups of submission data
5. **Access Control**: Strict admin-only access to submissions
