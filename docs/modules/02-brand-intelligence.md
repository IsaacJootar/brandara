# Module 02 — My Brand (Brand Intelligence)

## Purpose
Stores the brand's identity, voice, values, and guardrails.
Referenced by the AI in every content generation.
The richer this is filled out, the better every generated post.

## Screens
- `/my-brand` — overview with completion score
- `/my-brand/kit` — visual brand identity
- `/my-brand/profile` — vision, values, mission
- `/my-brand/voice` — Brand Voice setup and training

## Brand Kit fields
- Brand name and tagline
- Business description (what you do)
- Primary and secondary colour (hex codes)
- Font preference
- Logo upload
- Target audience description
- Key products, services, and offers

## Brand Profile fields
- **Vision:** Where does the brand want to be in 3 years?
- **Mission:** Why does the business exist?
- **Values:** 3–5 values with one-line explanation each
- **Negative brief:** What the brand never says, never sounds like
- **Target audience persona:** Who they speak to in plain language
- **Positioning:** How they differ from competitors

The negative brief is the most important field for AI accuracy.
Example: "We never use corporate jargon. We never make empty promises."

## Brand Voice setup
1. User pastes 10–20 past posts in a text area
2. OR answers 5 voice interview questions
3. System sends samples to Claude via BrandVoiceService
4. Claude returns a voice profile JSON object
5. Stored in `brands.brand_voice`
6. All future generation passes through this profile

Voice profile captures:
- Sentence length preference (short/medium/long)
- Vocabulary patterns and preferred words
- Tone characteristics (humour level, formality, directness)
- Structural preferences (lists vs prose, questions vs statements)
- Emoji usage habits

## Completion score
Dashboard shows a % score for brand profile completeness.
Incomplete fields show "Add this to improve your content quality."
Gamification drives users to fill in the full profile.

## Database tables
- `brands` — all fields including brand_voice JSON
- `media_files` — logo storage

## Agency mode
Each client brand managed by an agency has its own completely
isolated Brand Kit, Brand Profile, and Brand Voice.
Switching brands in the Clients screen switches all context.
