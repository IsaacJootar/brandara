# Prompt — AI Visibility Query Generation

## When to use
Called by `AiVisibilityService` to generate queries for monitoring brand
presence in AI answers. Also used to analyse AI responses for brand mentions.

## Prompt 1 — Generate relevant queries for a brand

### System prompt
```
Generate a list of questions that people might ask AI assistants when
looking for a business or professional like this brand.
Focus on discovery and recommendation queries.
Include location-specific queries for African markets.
Return valid JSON only.
```

### User prompt template
```
BRAND: {brand_name}
INDUSTRY: {industry}
SERVICES: {services_list}
LOCATION: {city}, {country}
TARGET AUDIENCE: {target_audience}

Generate 15 queries that someone might ask ChatGPT, Perplexity, or Google AI
when looking for this type of business. Include:
- 5 location-specific queries (e.g. "best X in Lagos")
- 5 industry/service queries (e.g. "who should I hire for X")
- 5 problem-solution queries (e.g. "how do I find a good X for Y")

Return this JSON:
{
  "queries": [
    {"query": "best marketing consultants in Lagos", "type": "location"},
    {"query": "who should I hire for social media management", "type": "service"},
    {"query": "how do I find a reliable accountant for my startup in Nigeria", "type": "problem"}
  ]
}
```

## Prompt 2 — Analyse AI response for brand mentions

### System prompt
```
You are analysing an AI-generated response to determine if a specific brand
is mentioned, how it is mentioned, and what sentiment surrounds the mention.
Return valid JSON only.
```

### User prompt template
```
BRAND NAME: {brand_name}
QUERY ASKED: {query}
AI RESPONSE: {ai_response_text}

Analyse this response. Return this JSON:
{
  "brand_mentioned": true/false,
  "mention_position": null or integer (1 = first mention),
  "mention_context": "exact quote or description of how brand is mentioned",
  "sentiment": "positive|neutral|negative|not_mentioned",
  "sentiment_reason": "brief explanation",
  "competing_brands_mentioned": ["brand1", "brand2"],
  "topics_covered": ["topic1", "topic2"],
  "recommendation_strength": "strong|moderate|weak|none"
}
```
