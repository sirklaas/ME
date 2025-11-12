# 🔴 URGENT: PocketBase Schema Update Required

## New Fields to Add to `submissions` Collection

You need to add these fields to your PocketBase `submissions` collection:

### 1. Chapter 9 Field
| Field Name | Type | Required | Description |
|------------|------|----------|-------------|
| `chapter09` | JSON | No | Film Maken (Questions 41-43) |

### 2. Character Generation Fields

**✅ Already Exist (no action needed):**
- `story_prompt1` - Story prompt for first video
- `story_prompt2` - Story prompt for second video  
- `story_prompt3` - Story prompt for final video

**🔴 Need to Add:**
| Field Name | Type | Required | Description |
|------------|------|----------|-------------|
| `character_name` | Text | No | Generated character name (e.g., "De Mysterieuze Vos") |
| `character_type` | Text | No | Type of character (e.g., "vos", "tomaat", "ridder") |
| `personality_traits` | Text (Long) | No | AI-generated personality description |
| `ai_summary` | Text (Long) | No | AI summary of the player's answers |
| `image_generation_prompt` | Text (Long) | No | Prompt for AI image generation |
| `character_generation_success` | Boolean | No | Whether character generation succeeded |

## How to Add These Fields in PocketBase

1. **Login to PocketBase Admin**
   - Go to: https://pinkmilk.pockethost.io/_/
   - Login with your admin credentials

2. **Navigate to Collections**
   - Click on "Collections" in the left sidebar
   - Find and click on the `submissions` collection

3. **Add Each Field**
   For each field above:
   - Click "+ New field"
   - Select the field type (Text, JSON, Boolean)
   - Enter the field name exactly as shown
   - Set "Required" to NO (optional fields)
   - For Text fields with "(Long)", enable "Use as textarea"
   - Click "Save"

4. **Save Collection**
   - After adding all fields, click "Save" on the collection

## What This Enables

✅ **Character Generation**: AI creates unique masked characters based on answers
✅ **Personality Analysis**: AI analyzes player responses and creates personality profiles
✅ **Story Prompts**: Three-level story progression for the gameshow videos
✅ **Image Generation**: AI-ready prompts for creating character images
✅ **Chapter 9 Support**: New movie-making questions (41-43)

## Testing After Update

1. Complete the questionnaire on the website
2. Check the browser console for:
   - `✅ Character data generated`
   - `✅ Saved to PocketBase successfully`
3. Check PocketBase admin to verify all fields are populated

## Troubleshooting

**If character generation fails:**
- Check that `api-keys.php` has a valid OpenAI API key
- Check browser console for error messages
- Character data will be saved as empty strings (won't break the form)

**If PocketBase save fails:**
- Check that all new fields exist in the collection
- Check PocketBase error logs in the admin panel
- Data is backed up in browser localStorage as fallback
