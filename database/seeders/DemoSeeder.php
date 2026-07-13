<?php

namespace Database\Seeders;

use App\Models\AiGeneratedAsset;
use App\Models\AiPresenceResult;
use App\Models\AiVisibilityCheck;
use App\Models\AiVisibilityReport;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\ContentPillar;
use App\Models\EngagementAction;
use App\Models\EngagementRule;
use App\Models\Lead;
use App\Models\MediaFile;
use App\Models\Notification;
use App\Models\PlatformConnection;
use App\Models\Post;
use App\Models\PostAnalytic;
use App\Models\Subscription;
use App\Models\TrackedKeyword;
use App\Models\TrendSignal;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Analytics\FakeAnalyticsSeeder;
use App\Services\Trends\FakeTrendsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LogicException;

class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public const PASSWORD = 'password';

    /**
     * @var array<int, array{
     *     workspace: array<string, mixed>,
     *     owner: array{name: string, email: string},
     *     brands: array<int, array<string, mixed>>
     * }>
     */
    public const ACCOUNTS = [
        [
            'workspace' => [
                'name' => 'Kora Advisory Demo',
                'slug' => 'brandara-demo-basic',
                'country' => 'NG',
                'timezone' => 'Africa/Lagos',
                'plan' => 'starter',
                'subscription_status' => 'trialing',
            ],
            'owner' => ['name' => 'Amaka Okafor', 'email' => 'demo.basic@brandara.test'],
            'brands' => [
                [
                    'name' => 'Kora Advisory',
                    'slug' => 'demo-kora-advisory',
                    'tagline' => 'Practical finance guidance for growing Nigerian businesses',
                    'description' => 'Kora Advisory helps small business owners understand cash flow, pricing, and sustainable growth.',
                    'target_audience' => 'Nigerian founders and owner-managed businesses with 5 to 50 employees.',
                    'positioning' => 'Clear, practical financial advice without jargon.',
                    'primary_color' => '#0F766E',
                    'secondary_color' => '#F59E0B',
                    'website_url' => 'https://example.com/kora-advisory',
                    'post_count' => 18,
                    'lead_count' => 6,
                    'media_count' => 2,
                    'platforms' => ['linkedin', 'twitter', 'facebook'],
                    'rich_features' => false,
                ],
            ],
        ],
        [
            'workspace' => [
                'name' => 'Northstar Growth Demo',
                'slug' => 'brandara-demo-growth',
                'country' => 'GH',
                'timezone' => 'Africa/Accra',
                'plan' => 'pro',
                'subscription_status' => 'active',
            ],
            'owner' => ['name' => 'Kwame Mensah', 'email' => 'demo.growth@brandara.test'],
            'brands' => [
                [
                    'name' => 'Northstar Growth Studio',
                    'slug' => 'demo-northstar-growth',
                    'tagline' => 'Growth systems for ambitious African service businesses',
                    'description' => 'Northstar helps consultants and agencies turn expertise into consistent demand through positioning and content.',
                    'target_audience' => 'B2B consultants, agency founders, and professional service businesses across West Africa.',
                    'positioning' => 'Local market understanding combined with measurable growth systems.',
                    'primary_color' => '#7C3AED',
                    'secondary_color' => '#14B8A6',
                    'website_url' => 'https://example.com/northstar-growth',
                    'post_count' => 32,
                    'lead_count' => 20,
                    'media_count' => 4,
                    'platforms' => ['linkedin', 'twitter', 'facebook', 'instagram', 'threads'],
                    'rich_features' => true,
                ],
            ],
        ],
        [
            'workspace' => [
                'name' => 'Lagos Launch Lab Demo',
                'slug' => 'brandara-demo-agency',
                'country' => 'NG',
                'timezone' => 'Africa/Lagos',
                'plan' => 'agency',
                'subscription_status' => 'active',
            ],
            'owner' => ['name' => 'Tunde Adebayo', 'email' => 'demo.agency@brandara.test'],
            'brands' => [
                [
                    'name' => 'Lagos Launch Lab',
                    'slug' => 'demo-lagos-launch-lab',
                    'tagline' => 'Launch strategy and content for African technology companies',
                    'description' => 'Lagos Launch Lab plans product launches and founder-led campaigns for technology companies entering African markets.',
                    'target_audience' => 'Technology founders, product leaders, and venture-backed teams expanding across Africa.',
                    'positioning' => 'Senior launch strategy with hands-on local execution.',
                    'primary_color' => '#DC2626',
                    'secondary_color' => '#2563EB',
                    'website_url' => 'https://example.com/lagos-launch-lab',
                    'post_count' => 28,
                    'lead_count' => 24,
                    'media_count' => 4,
                    'platforms' => ['linkedin', 'twitter', 'facebook', 'instagram', 'threads', 'tiktok'],
                    'rich_features' => true,
                ],
                [
                    'name' => 'Mira Foods',
                    'slug' => 'demo-mira-foods',
                    'tagline' => 'Modern pantry staples made from West African ingredients',
                    'description' => 'Mira Foods makes convenient pantry products using responsibly sourced ingredients from West African farmers.',
                    'target_audience' => 'Urban families and food lovers in Nigeria, Ghana, and the United Kingdom.',
                    'positioning' => 'Familiar West African flavour in convenient modern formats.',
                    'primary_color' => '#15803D',
                    'secondary_color' => '#EAB308',
                    'website_url' => 'https://example.com/mira-foods',
                    'post_count' => 14,
                    'lead_count' => 10,
                    'media_count' => 3,
                    'platforms' => ['instagram', 'facebook', 'tiktok'],
                    'rich_features' => true,
                ],
            ],
        ],
    ];

    /** @var array<int, string> */
    private const TOPICS = [
        'The pricing mistake that quietly drains growing businesses',
        'Three signs your content strategy needs a reset',
        'What our best client result taught us about consistency',
        'A simple weekly planning habit for busy founders',
        'Why clear positioning matters more than posting every day',
        'Behind the scenes of a successful customer campaign',
        'Five questions to ask before entering a new market',
        'The difference between attention and useful demand',
        'A client lesson we wish every founder knew earlier',
        'How to turn one strong idea into a week of content',
        'What African buyers expect from trusted business brands',
        'A practical way to measure whether content is working',
        'Stop copying global advice that ignores your local market',
        'The small follow-up habit that creates repeat business',
        'How customer stories make expertise easier to trust',
        'What we changed after a campaign missed its target',
    ];

    /** @var array<int, array{name: string, headline: string, company: string}> */
    private const LEADS = [
        ['name' => 'Chinedu Eze', 'headline' => 'Founder', 'company' => 'Nexa Commerce'],
        ['name' => 'Adwoa Boateng', 'headline' => 'Marketing Director', 'company' => 'Sankofa Health'],
        ['name' => 'Wanjiku Njoroge', 'headline' => 'Operations Lead', 'company' => 'Jenga Works'],
        ['name' => 'Thabo Mokoena', 'headline' => 'Managing Partner', 'company' => 'Mokoena Advisory'],
        ['name' => 'Aisha Bello', 'headline' => 'Product Manager', 'company' => 'PayBridge'],
        ['name' => 'Kofi Asare', 'headline' => 'Agency Founder', 'company' => 'Accra Creative Co'],
        ['name' => 'Nneka Obi', 'headline' => 'Chief Executive Officer', 'company' => 'Bloom Retail'],
        ['name' => 'Brian Otieno', 'headline' => 'Growth Manager', 'company' => 'Savanna Cloud'],
        ['name' => 'Zainab Yusuf', 'headline' => 'Business Owner', 'company' => 'Zee Naturals'],
        ['name' => 'Lerato Dlamini', 'headline' => 'Brand Strategist', 'company' => 'Ubuntu Studio'],
    ];

    public function run(): void
    {
        $this->assertSafeEnvironment();

        DB::transaction(function (): void {
            foreach (self::ACCOUNTS as $account) {
                $workspace = $this->seedWorkspace($account['workspace'], $account['owner']['email']);
                $owner = $this->seedOwner($workspace, $account['owner']);

                $brands = [];
                foreach ($account['brands'] as $brandData) {
                    $brands[] = [$this->seedBrand($workspace, $brandData), $brandData];
                }

                $this->resetWorkspaceDemoData($workspace, $owner, array_column($brands, 0));
                $this->seedSubscriptions($workspace);

                foreach ($brands as [$brand, $brandData]) {
                    $this->seedBrandData($brand, $owner, $brandData);
                }
            }
        });
    }

    private function assertSafeEnvironment(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('Demo data can only be seeded in local or testing environments.');
        }
    }

    /** @param array<string, mixed> $data */
    private function seedWorkspace(array $data, string $ownerEmail): Workspace
    {
        $existing = Workspace::where('slug', $data['slug'])->first();

        if ($existing && $existing->owner_email !== $ownerEmail) {
            throw new LogicException("Workspace slug '{$data['slug']}' is already used by non-demo data.");
        }

        $emailOwner = User::where('email', $ownerEmail)->first();
        if ($emailOwner && (! $existing || $emailOwner->workspace_id !== $existing->id)) {
            throw new LogicException("Demo email '{$ownerEmail}' is already used by another workspace.");
        }

        return Workspace::updateOrCreate(
            ['slug' => $data['slug']],
            [
                ...$data,
                'owner_email' => $ownerEmail,
                'language' => 'en',
                'trial_ends_at' => $data['subscription_status'] === 'trialing' ? now()->addDays(7) : null,
                'ai_generations_used' => $data['plan'] === 'starter' ? 12 : 46,
                'usage_reset_date' => now()->startOfMonth(),
            ],
        );
    }

    /** @param array{name: string, email: string} $data */
    private function seedOwner(Workspace $workspace, array $data): User
    {
        return User::updateOrCreate(
            ['email' => $data['email']],
            [
                'workspace_id' => $workspace->id,
                'name' => $data['name'],
                'password' => Hash::make(self::PASSWORD),
                'role' => 'owner',
                'last_active_at' => now()->subMinutes(12),
            ],
        );
    }

    /** @param array<string, mixed> $data */
    private function seedBrand(Workspace $workspace, array $data): Brand
    {
        return Brand::updateOrCreate(
            ['workspace_id' => $workspace->id, 'slug' => $data['slug']],
            [
                'name' => $data['name'],
                'tagline' => $data['tagline'],
                'description' => $data['description'],
                'vision' => 'Become one of the most trusted brands in our category across Africa within three years.',
                'mission' => 'Help ambitious African businesses grow with practical ideas, reliable service, and clear communication.',
                'values' => [
                    ['title' => 'Clarity', 'description' => 'Make complex decisions easier to understand.'],
                    ['title' => 'Useful work', 'description' => 'Create practical value before asking for attention.'],
                    ['title' => 'Local insight', 'description' => 'Respect how African markets and customers actually work.'],
                ],
                'target_audience' => $data['target_audience'],
                'negative_brief' => 'Avoid empty hype, imported jargon, fake urgency, and claims that cannot be supported.',
                'positioning' => $data['positioning'],
                'primary_color' => $data['primary_color'],
                'secondary_color' => $data['secondary_color'],
                'font_preference' => 'Inter',
                'brand_voice' => [
                    'writing_summary' => 'Clear, confident, practical, and warm. Uses short paragraphs, concrete examples, and direct advice.',
                    'sentence_length' => 'Mostly short and medium sentences',
                    'vocabulary' => 'Plain business English with familiar African market references',
                    'tone' => 'Helpful expert, never preachy',
                    'opening_patterns' => ['Direct observation', 'Useful question', 'Short client story'],
                    'closing_patterns' => ['Practical next step', 'Reflective question'],
                    'emoji_habits' => 'Rare and purposeful',
                    'signature_phrases' => ['Here is the practical part', 'Start with what your customer needs'],
                ],
                'voice_samples_count' => 8,
                'default_tone' => 'professional',
                'language' => 'en',
                'website_url' => $data['website_url'],
            ],
        );
    }

    /** @param array<int, Brand> $brands */
    private function resetWorkspaceDemoData(Workspace $workspace, User $owner, array $brands): void
    {
        $brandIds = collect($brands)->pluck('id');

        Notification::where('user_id', $owner->id)->whereIn('brand_id', $brandIds)->delete();
        Subscription::where('workspace_id', $workspace->id)
            ->where('provider_reference', 'like', 'brandara-demo-%')
            ->delete();

        foreach ($brands as $brand) {
            PostAnalytic::where('brand_id', $brand->id)->delete();
            EngagementAction::where('brand_id', $brand->id)->delete();
            EngagementRule::where('brand_id', $brand->id)->delete();
            TrackedKeyword::where('brand_id', $brand->id)->delete();
            TrendSignal::where('brand_id', $brand->id)->delete();
            AiPresenceResult::where('brand_id', $brand->id)->delete();
            AiVisibilityCheck::where('brand_id', $brand->id)->delete();
            AiVisibilityReport::where('brand_id', $brand->id)->delete();
            AiGeneratedAsset::where('brand_id', $brand->id)->delete();
            Lead::where('brand_id', $brand->id)->delete();
            PlatformConnection::where('brand_id', $brand->id)->delete();
            Post::where('brand_id', $brand->id)->delete();
            Campaign::where('brand_id', $brand->id)->delete();
            ContentPillar::where('brand_id', $brand->id)->delete();
            MediaFile::where('brand_id', $brand->id)->delete();
            Storage::disk('public')->deleteDirectory("brands/{$brand->id}/media/demo");
        }
    }

    /** @param array<string, mixed> $profile */
    private function seedBrandData(Brand $brand, User $owner, array $profile): void
    {
        $media = $this->seedMedia($brand, $owner, (int) $profile['media_count']);
        $pillars = $this->seedPillars($brand, (bool) $profile['rich_features']);
        $campaigns = $this->seedCampaigns($brand, (bool) $profile['rich_features']);
        $posts = $this->seedPosts($brand, $owner, $profile, $pillars, $campaigns, $media);

        $this->seedConnections($brand, $profile['platforms']);
        $this->seedAnalytics($brand);
        $this->seedLeads($brand, (int) $profile['lead_count']);
        $this->seedNotifications($brand, $owner);

        if ($profile['rich_features']) {
            $this->seedEngagement($brand);
            $this->seedTrends($brand);
            $this->seedAiPresence($brand);
        }
    }

    /** @return array<int, MediaFile> */
    private function seedMedia(Brand $brand, User $owner, int $count): array
    {
        $palette = [
            ['background' => [124, 58, 237], 'accent' => [20, 184, 166]],
            ['background' => [15, 118, 110], 'accent' => [245, 158, 11]],
            ['background' => [37, 99, 235], 'accent' => [220, 38, 38]],
            ['background' => [21, 128, 61], 'accent' => [234, 179, 8]],
        ];
        $files = [];

        for ($index = 0; $index < $count; $index++) {
            $filename = 'demo-social-'.($index + 1).'.png';
            $path = "brands/{$brand->id}/media/demo/{$filename}";
            $image = $this->makeDemoImage(
                $brand->name,
                self::TOPICS[$index % count(self::TOPICS)],
                $palette[$index % count($palette)],
            );
            Storage::disk('public')->put($path, $image);

            $files[] = MediaFile::create([
                'brand_id' => $brand->id,
                'uploaded_by' => $owner->id,
                'filename' => $filename,
                'storage_path' => $path,
                'mime_type' => 'image/png',
                'file_size_kb' => max(1, (int) ceil(strlen($image) / 1024)),
                'width' => 1200,
                'height' => 630,
                'alt_text' => "Branded social graphic for {$brand->name}",
                'tags' => ['demo', 'campaign', 'social'],
            ]);
        }

        return $files;
    }

    /**
     * @param  array{background: array{int, int, int}, accent: array{int, int, int}}  $palette
     */
    private function makeDemoImage(string $brandName, string $topic, array $palette): string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true);
        }

        $image = imagecreatetruecolor(1200, 630);
        $background = imagecolorallocate($image, ...$palette['background']);
        $accent = imagecolorallocate($image, ...$palette['accent']);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $background);
        imagefilledrectangle($image, 0, 0, 28, 630, $accent);
        imagefilledrectangle($image, 72, 90, 1128, 94, $accent);
        imagestring($image, 5, 74, 44, Str::upper(Str::limit($brandName, 42, '')), $white);

        $lines = explode("\n", wordwrap(Str::limit($topic, 110), 42));
        foreach (array_slice($lines, 0, 4) as $line => $text) {
            imagestring($image, 5, 74, 185 + ($line * 48), $text, $white);
        }

        imagestring($image, 4, 74, 552, 'Demo content for Brandara testing', $white);
        ob_start();
        imagepng($image, null, 7);
        $contents = ob_get_clean();
        imagedestroy($image);

        return is_string($contents) ? $contents : '';
    }

    /** @return array<int, ContentPillar> */
    private function seedPillars(Brand $brand, bool $rich): array
    {
        $definitions = [
            ['name' => 'Expert Guidance', 'goal' => 'authority', 'color' => '#7C3AED'],
            ['name' => 'Customer Stories', 'goal' => 'trust', 'color' => '#2563EB'],
            ['name' => 'Market Insights', 'goal' => 'awareness', 'color' => '#0F766E'],
            ['name' => 'Offers and Services', 'goal' => 'conversion', 'color' => '#DC2626'],
            ['name' => 'Founder Journey', 'goal' => 'trust', 'color' => '#D97706'],
        ];

        return collect(array_slice($definitions, 0, $rich ? 5 : 3))
            ->map(fn (array $pillar, int $index) => ContentPillar::create([
                'brand_id' => $brand->id,
                ...$pillar,
                'sort_order' => $index + 1,
                'is_active' => true,
            ]))
            ->all();
    }

    /** @return array<int, Campaign> */
    private function seedCampaigns(Brand $brand, bool $rich): array
    {
        $definitions = [
            ['name' => 'Quarterly Authority Series', 'status' => 'active', 'start' => -14, 'end' => 35],
            ['name' => 'Customer Results Month', 'status' => 'active', 'start' => -5, 'end' => 24],
            ['name' => 'Founder Story Campaign', 'status' => 'draft', 'start' => 21, 'end' => 50],
            ['name' => 'Mid-Year Growth Review', 'status' => 'completed', 'start' => -75, 'end' => -45],
        ];

        return collect(array_slice($definitions, 0, $rich ? 4 : 2))
            ->map(fn (array $campaign, int $index) => Campaign::create([
                'brand_id' => $brand->id,
                'name' => $campaign['name'],
                'type' => 'custom',
                'goal' => $index === 1 ? 'Build trust with concrete customer outcomes.' : 'Increase qualified awareness and conversations.',
                'key_message' => 'Useful expertise creates confidence before a sales conversation begins.',
                'start_date' => now()->addDays($campaign['start'])->toDateString(),
                'end_date' => now()->addDays($campaign['end'])->toDateString(),
                'platforms' => ['linkedin', 'instagram', 'facebook'],
                'tone' => 'professional',
                'ai_summary' => 'A focused campaign combining expert advice, customer evidence, and clear next steps.',
                'whatsapp_broadcast' => 'We are sharing practical lessons from recent client work this month. Reply if you would like the full guide.',
                'status' => $campaign['status'],
            ]))
            ->all();
    }

    /**
     * @param  array<string, mixed>  $profile
     * @param  array<int, ContentPillar>  $pillars
     * @param  array<int, Campaign>  $campaigns
     * @param  array<int, MediaFile>  $media
     * @return array<int, Post>
     */
    private function seedPosts(Brand $brand, User $owner, array $profile, array $pillars, array $campaigns, array $media): array
    {
        $statuses = $this->postStatuses((int) $profile['post_count']);
        $posts = [];

        foreach ($statuses as $index => $status) {
            $topic = self::TOPICS[$index % count(self::TOPICS)];
            $createdAt = now()->subDays(max(1, count($statuses) - $index));
            $publishedAt = $status === 'published' ? now()->subDays(($index % 45) + 1)->setTime(9 + ($index % 7), 15) : null;
            $scheduledAt = match ($status) {
                'scheduled' => now()->addDays(($index % 10) + 1)->setTime(8 + ($index % 9), 30),
                'failed', 'cancelled' => now()->subDays(($index % 7) + 1)->setTime(10, 0),
                default => null,
            };
            $platformContents = $this->platformContents($topic, $profile['platforms'], $index);
            $mediaIds = $index % 3 === 0 && $media !== []
                ? [$media[$index % count($media)]->id]
                : [];

            $post = Post::create([
                'brand_id' => $brand->id,
                'content_pillar_id' => $pillars[$index % count($pillars)]->id,
                'campaign_id' => $campaigns[$index % count($campaigns)]->id,
                'created_by' => $owner->id,
                'approved_by' => $status === 'published' ? $owner->id : null,
                'title' => '[Demo] '.Str::limit($topic, 70, '').' '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                'input_type' => ['topic', 'transcript', 'product', 'manual'][$index % 4],
                'raw_input' => $topic,
                'ai_generated' => $index % 4 !== 3,
                'variation_selected' => ['authority', 'story', 'bold'][$index % 3],
                'platform_contents' => $platformContents,
                'tone' => ['professional', 'founder', 'educational', 'bold'][$index % 4],
                'media_ids' => $mediaIds,
                'status' => $status,
                'scheduled_at' => $scheduledAt,
                'published_at' => $publishedAt,
                'failure_reason' => $status === 'failed'
                    ? ($index % 2 === 0 ? 'The platform connection expired. Reconnect it and try again.' : 'The platform was temporarily unavailable. Try again now.')
                    : null,
                'retry_count' => $status === 'failed' ? 3 : 0,
                'live_post_urls' => null,
                'is_evergreen' => $index % 6 === 0,
                'last_recycled_at' => $index % 6 === 0 ? now()->subDays(75) : null,
            ]);
            $post->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt->copy()->addHours(2)])->save();
            $posts[] = $post;
        }

        return $posts;
    }

    /** @return array<int, string> */
    private function postStatuses(int $count): array
    {
        $published = max(6, (int) floor($count * 0.5));
        $scheduled = max(2, (int) floor($count * 0.15));
        $draft = max(2, (int) floor($count * 0.15));
        $remaining = $count - $published - $scheduled - $draft;
        $review = max(1, (int) floor($remaining / 3));
        $failed = max(1, (int) floor(($remaining - $review) / 2));
        $cancelled = max(0, $remaining - $review - $failed);

        return array_slice([
            ...array_fill(0, $published, 'published'),
            ...array_fill(0, $scheduled, 'scheduled'),
            ...array_fill(0, $draft, 'draft'),
            ...array_fill(0, $review, 'in_review'),
            ...array_fill(0, $failed, 'failed'),
            ...array_fill(0, $cancelled, 'cancelled'),
        ], 0, $count);
    }

    /**
     * @param  array<int, string>  $platforms
     * @return array<string, array{body: string, hashtags?: array<int, string>}>
     */
    private function platformContents(string $topic, array $platforms, int $index): array
    {
        $contents = [];

        foreach ($platforms as $platform) {
            $contents[$platform] = match ($platform) {
                'twitter' => ['body' => Str::limit("{$topic}. Here is the practical lesson: clarity creates action. What would you add?", 270)],
                'instagram' => ['body' => "{$topic}\n\nA practical lesson from our work: clear ideas earn trust when people can use them immediately.", 'hashtags' => ['AfricanBusiness', 'BrandGrowth', 'FounderLessons']],
                'tiktok' => ['body' => "Hook: {$topic}\nThree practical points, one example, and a clear next step for growing businesses.", 'hashtags' => ['BusinessTok', 'AfricanFounders']],
                'threads' => ['body' => "{$topic}.\n\nThis sounds simple, but most teams skip the practical part. Clear message, useful example, one next step."],
                default => ['body' => "{$topic}\n\nWe have seen this pattern repeatedly: businesses grow faster when their message is clear, useful, and connected to a real customer problem.\n\nStart with one practical change this week, measure the response, and improve from there.", 'hashtags' => $index % 2 === 0 ? ['BusinessGrowth', 'AfricanFounders'] : []],
            };
        }

        return $contents;
    }

    /** @param array<int, string> $platforms */
    private function seedConnections(Brand $brand, array $platforms): void
    {
        $connectablePlatforms = array_values(array_intersect(
            $platforms,
            ['linkedin', 'twitter', 'facebook', 'instagram', 'threads'],
        ));

        foreach ($connectablePlatforms as $index => $platform) {
            $status = match (true) {
                $index < 3 => 'connected',
                $index === 3 => 'expired',
                default => 'error',
            };

            PlatformConnection::create([
                'brand_id' => $brand->id,
                'platform' => $platform,
                'platform_user_id' => "demo-{$brand->slug}-{$platform}",
                'platform_username' => '@'.Str::of($brand->slug)->replace('demo-', '')->replace('-', ''),
                'access_token' => Crypt::encryptString("demo-access-token-{$platform}"),
                'refresh_token' => Crypt::encryptString("demo-refresh-token-{$platform}"),
                'token_expires_at' => $status === 'expired' ? now()->subDay() : now()->addDays(45),
                'status' => $status,
                'last_posted_at' => $status === 'connected' ? now()->subDays($index + 1) : null,
                'follower_count' => 1200 + ($index * 875),
            ]);
        }
    }

    private function seedAnalytics(Brand $brand): void
    {
        mt_srand((int) sprintf('%u', crc32($brand->slug)));
        app(FakeAnalyticsSeeder::class)->seed($brand, 90);
    }

    private function seedLeads(Brand $brand, int $count): void
    {
        $tags = ['warm_lead', 'prospect', 'client', 'partner', 'other', null];
        $platforms = ['linkedin', 'instagram', 'facebook', 'twitter'];

        for ($index = 0; $index < $count; $index++) {
            $person = self::LEADS[$index % count(self::LEADS)];
            Lead::create([
                'brand_id' => $brand->id,
                'platform' => $platforms[$index % count($platforms)],
                'platform_user_id' => "demo-lead-{$brand->slug}-{$index}",
                'name' => $person['name'].($index >= count(self::LEADS) ? ' '.(intdiv($index, count(self::LEADS)) + 1) : ''),
                'headline' => $person['headline'],
                'company' => $person['company'],
                'profile_url' => null,
                'tag' => $tags[$index % count($tags)],
                'notes' => $index % 3 === 0 ? 'Engaged with the customer results post. Follow up with a relevant case study.' : null,
                'follow_up_at' => $index % 4 === 0 ? now()->addDays(($index % 7) - 2)->toDateString() : null,
                'total_engagements' => 1 + (($index * 3) % 18),
                'last_engaged_at' => now()->subDays($index % 28)->subHours($index % 12),
            ]);
        }
    }

    private function seedNotifications(Brand $brand, User $owner): void
    {
        $definitions = [
            ['type' => 'post_failed', 'title' => 'A scheduled post needs attention', 'message' => 'Your LinkedIn post did not publish because the connection expired. Reconnect LinkedIn and retry it.', 'route' => 'schedule'],
            ['type' => 'approval_needed', 'title' => 'A post is waiting for review', 'message' => 'A draft customer story is ready for your review before it is scheduled.', 'route' => 'schedule'],
            ['type' => 'token_expired', 'title' => 'Reconnect one platform', 'message' => 'Your X connection has expired. Reconnect it to keep scheduled posts moving.', 'route' => 'connections'],
            ['type' => 'weekly_report', 'title' => 'Your weekly results are ready', 'message' => 'Reach increased this week and your customer story was the strongest post.', 'route' => 'results'],
            ['type' => 'ai_visibility_alert', 'title' => 'Your AI Presence score changed', 'message' => 'Your website readiness score improved after new business details were detected.', 'route' => 'ai-presence'],
        ];

        foreach ($definitions as $index => $notification) {
            Notification::create([
                'user_id' => $owner->id,
                'brand_id' => $brand->id,
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'action_url' => "/{$brand->slug}/{$notification['route']}",
                'channels' => ['in_app', 'mail'],
                'read_at' => $index >= 3 ? now()->subDays($index) : null,
                'sent_at' => now()->subHours(($index + 1) * 4),
            ]);
        }
    }

    private function seedSubscriptions(Workspace $workspace): void
    {
        if ($workspace->plan === 'starter') {
            return;
        }

        Subscription::create([
            'workspace_id' => $workspace->id,
            'plan' => $workspace->plan,
            'interval' => 'monthly',
            'currency' => $workspace->country === 'NG' ? 'NGN' : 'GHS',
            'amount' => $workspace->plan === 'agency' ? 140000 : 590,
            'status' => 'active',
            'provider' => $workspace->country === 'NG' ? 'paystack' : 'flutterwave',
            'provider_reference' => "brandara-demo-active-{$workspace->slug}",
            'provider_subscription_id' => "demo-subscription-{$workspace->slug}",
            'provider_customer_id' => "demo-customer-{$workspace->slug}",
            'current_period_start' => now()->subDays(10),
            'current_period_end' => now()->addDays(20),
        ]);

        if ($workspace->plan === 'agency') {
            $old = Subscription::create([
                'workspace_id' => $workspace->id,
                'plan' => 'pro',
                'interval' => 'monthly',
                'currency' => 'NGN',
                'amount' => 60000,
                'status' => 'cancelled',
                'provider' => 'paystack',
                'provider_reference' => "brandara-demo-cancelled-{$workspace->slug}",
                'current_period_start' => now()->subMonths(2),
                'current_period_end' => now()->subMonth(),
                'cancelled_at' => now()->subMonth(),
            ]);
            $old->forceFill(['created_at' => now()->subMonths(2), 'updated_at' => now()->subMonth()])->save();
        }
    }

    private function seedEngagement(Brand $brand): void
    {
        $commentRule = EngagementRule::create([
            'brand_id' => $brand->id,
            'type' => 'auto_comment',
            'platform' => 'linkedin',
            'target_accounts' => ['@techcabal', '@businessdayng'],
            'target_keywords' => ['African founders', 'business growth', 'customer trust'],
            'target_industry' => 'Professional services',
            'daily_limit' => 8,
            'require_review' => true,
            'comment_tone' => 'professional',
            'is_active' => true,
            'actions_today' => 3,
            'actions_reset_date' => now()->toDateString(),
        ]);
        $likeRule = EngagementRule::create([
            'brand_id' => $brand->id,
            'type' => 'auto_like',
            'platform' => 'instagram',
            'target_accounts' => ['@africabusiness', '@foundersafrica'],
            'target_keywords' => ['small business', 'made in Africa'],
            'target_industry' => 'African business',
            'daily_limit' => 15,
            'require_review' => false,
            'comment_tone' => null,
            'is_active' => true,
            'actions_today' => 6,
            'actions_reset_date' => now()->toDateString(),
        ]);

        $statuses = ['pending', 'pending', 'posted', 'posted', 'failed', 'skipped', 'posted', 'pending'];
        foreach ($statuses as $index => $status) {
            $rule = $index % 3 === 0 ? $likeRule : $commentRule;
            $type = $rule->type === 'auto_like' ? 'like' : 'comment';
            EngagementAction::create([
                'brand_id' => $brand->id,
                'rule_id' => $rule->id,
                'type' => $type,
                'platform' => $rule->platform,
                'target_post_id' => "demo-engagement-post-{$index}",
                'target_account' => $index % 2 === 0 ? '@techcabal' : '@africabusiness',
                'target_post_excerpt' => 'African businesses win when useful local insight is paired with consistent execution.',
                'comment_body' => $type === 'comment' ? 'Strong point. The local execution detail is often what turns a good strategy into a result customers can feel.' : null,
                'status' => $status,
                'failure_reason' => $status === 'failed' ? 'The platform was temporarily unavailable.' : null,
                'posted_at' => $status === 'posted' ? now()->subHours($index + 2) : null,
            ]);
        }
    }

    private function seedTrends(Brand $brand): void
    {
        app(FakeTrendsSeeder::class)->seed($brand);

        foreach ([
            ['keyword' => $brand->name, 'platform' => 'all'],
            ['keyword' => 'African business growth', 'platform' => 'linkedin'],
            ['keyword' => 'founder-led content', 'platform' => 'twitter'],
        ] as $keyword) {
            TrackedKeyword::create(['brand_id' => $brand->id, ...$keyword]);
        }
    }

    private function seedAiPresence(Brand $brand): void
    {
        $results = [
            'has_https' => 'pass', 'site_loads' => 'pass', 'has_title_tag' => 'pass',
            'has_meta_description' => 'pass', 'has_canonical_tag' => 'pass', 'has_json_ld_schema' => 'fail',
            'has_faq_schema' => 'fail', 'has_about_page' => 'pass', 'has_contact_page' => 'pass',
            'has_contact_details_on_site' => 'pass', 'mentions_city' => 'pass', 'mentions_industry' => 'pass',
            'has_robots_txt' => 'pass', 'has_xml_sitemap' => 'fail', 'has_sameas_links' => 'pass',
            'page_indexable' => 'pass', 'ai_bots_allowed' => 'fail', 'canonical_matches_url' => 'pass',
            'has_mobile_viewport' => 'pass', 'has_local_business_schema' => 'fail',
        ];

        AiVisibilityCheck::create([
            'brand_id' => $brand->id,
            'website_url' => $brand->website_url,
            'results' => $results,
            'manual_checks' => [
                'has_google_business_profile' => true,
                'nap_consistent' => true,
                'has_ten_plus_reviews' => false,
                'has_three_plus_listings' => false,
                'social_profiles_linked' => true,
            ],
            'score' => 68,
            'tier1_passed' => 12,
            'tier2_passed' => 3,
            'tier3_passed' => 3,
            'scanned_at' => now()->subDays(9),
        ]);

        $prompts = [
            ['category' => 'discovery', 'text' => "Best business growth partners in {$brand->workspace->country}"],
            ['category' => 'discovery', 'text' => "Top African firms for {$brand->positioning}"],
            ['category' => 'trust', 'text' => "Who is {$brand->name} and what do they do?"],
            ['category' => 'local_intent', 'text' => 'Recommended African business partners for growing companies'],
            ['category' => 'consideration', 'text' => 'What should I look for when choosing a growth partner?'],
            ['category' => 'trust', 'text' => "Is {$brand->name} a reliable company?"],
        ];
        $providers = ['claude', 'chatgpt', 'gemini'];

        foreach ($prompts as $index => $prompt) {
            $appeared = $index !== 4;
            AiPresenceResult::create([
                'brand_id' => $brand->id,
                'provider' => $providers[$index % count($providers)],
                'prompt' => $prompt['text'],
                'prompt_category' => $prompt['category'],
                'appeared' => $appeared,
                'position' => $appeared ? (($index % 3) + 1) : null,
                'sentiment' => $appeared ? ($index % 2 === 0 ? 'positive' : 'neutral') : 'not_mentioned',
                'raw_response' => $appeared
                    ? "{$brand->name} is one of the relevant African businesses to consider because of its practical market focus."
                    : 'Several general selection factors were discussed without naming the brand.',
                'competitors_mentioned' => $index % 2 === 0 ? ['Demo Market Leader', 'Regional Growth Co'] : [],
                'queried_at' => now()->subDays(12),
            ]);
        }

        foreach (['chatgpt', 'gemini', 'claude'] as $index => $system) {
            AiVisibilityReport::create([
                'brand_id' => $brand->id,
                'ai_system' => $system,
                'query' => "What companies help African businesses grow in {$brand->workspace->country}?",
                'response_text' => "{$brand->name} appeared as a relevant option alongside other regional providers.",
                'brand_mentioned' => $index !== 1,
                'mention_position' => $index !== 1 ? $index + 1 : null,
                'sentiment' => $index === 0 ? 'positive' : 'neutral',
                'topics' => ['business growth', 'African markets', 'brand strategy'],
                'report_date' => now()->subDays(12)->toDateString(),
            ]);
        }

        foreach ([
            ['type' => 'json_ld', 'status' => 'published', 'content' => '{"@context":"https://schema.org","@type":"Organization","name":"'.addslashes($brand->name).'"}'],
            ['type' => 'about_copy', 'status' => 'draft', 'content' => "{$brand->name} helps ambitious African businesses grow through clear strategy, useful content, and reliable execution."],
            ['type' => 'faq_schema', 'status' => 'draft', 'content' => '{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[]}'],
        ] as $asset) {
            AiGeneratedAsset::create([
                'brand_id' => $brand->id,
                ...$asset,
                'generated_at' => now()->subDays(6),
            ]);
        }
    }
}
