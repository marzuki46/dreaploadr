<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Models\Setting;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use BackedEnum;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'General Settings';
    protected static ?string $title = 'General Settings';
    protected string $view = 'filament.super-admin.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'ai_provider' => Setting::getVal('ai_provider', 'gemini'),
            'ai_gemini_api_key' => Setting::getVal('ai_gemini_api_key', ''),
            'ai_naim_router_key' => Setting::getVal('ai_naim_router_key', ''),
            'affiliate_cookie_days' => Setting::getVal('affiliate_cookie_days', 30),
            'midtrans_server_key' => Setting::getVal('midtrans_server_key', ''),
            'midtrans_client_key' => Setting::getVal('midtrans_client_key', ''),
            'midtrans_is_production' => Setting::getVal('midtrans_is_production', '0'),
            'enable_facebook' => (bool) Setting::getVal('enable_facebook', true),
            'facebook_cookie' => Setting::getVal('facebook_cookie', ''),
            'facebook_client_id' => Setting::getVal('facebook_client_id', ''),
            'facebook_client_secret' => Setting::getVal('facebook_client_secret', ''),
            'facebook_redirect_uri' => Setting::getVal('facebook_redirect_uri', ''),
            'enable_youtube' => (bool) Setting::getVal('enable_youtube', true),
            'youtube_client_id' => Setting::getVal('youtube_client_id', ''),
            'youtube_client_secret' => Setting::getVal('youtube_client_secret', ''),
            'youtube_redirect_uri' => Setting::getVal('youtube_redirect_uri', ''),
            'enable_tiktok' => (bool) Setting::getVal('enable_tiktok', true),
            'tiktok_client_key' => Setting::getVal('tiktok_client_key', ''),
            'tiktok_client_secret' => Setting::getVal('tiktok_client_secret', ''),
            'tiktok_redirect_uri' => Setting::getVal('tiktok_redirect_uri', ''),
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('AI Provider')
                            ->icon('heroicon-o-cpu-chip')
                            ->schema([
                                Placeholder::make('ai_guide')
                                    ->label('Petunjuk')
                                    ->content(new HtmlString('Dapatkan Gemini API Key di <a href="https://aistudio.google.com/" target="_blank" class="text-indigo-600 underline">Google AI Studio</a>.')),
                                Select::make('ai_provider')
                                    ->label('Active AI Provider')
                                    ->options([
                                        'gemini' => 'Google Gemini API',
                                        'naim' => 'Naim Router',
                                    ])
                                    ->required(),
                                TextInput::make('ai_gemini_api_key')
                                    ->label('Google Gemini API Key')
                                    ->password(),
                                TextInput::make('ai_naim_router_key')
                                    ->label('Naim Router API Key')
                                    ->password(),
                            ]),

                        Tab::make('Facebook API')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Placeholder::make('fb_guide')
                                    ->label('Petunjuk')
                                    ->content(new HtmlString('Dapatkan App ID dan Secret dengan membuat aplikasi tipe Business di <a href="https://developers.facebook.com/" target="_blank" class="text-indigo-600 underline">Meta for Developers</a>. Pastikan menambahkan produk Facebook Login.')),
                                Toggle::make('enable_facebook')
                                    ->label('Enable Facebook Integration')
                                    ->helperText('Izinkan pengguna untuk menghubungkan dan memposting ke Facebook.'),
                                TextInput::make('facebook_cookie')
                                    ->label('Raw Facebook Cookie (c_user & xs)')
                                    ->helperText('Diperlukan untuk fitur Scraper Keyword Global. Gunakan akun dummy/fake.')
                                    ->password(),
                                TextInput::make('facebook_client_id')
                                    ->label('Facebook App ID'),
                                TextInput::make('facebook_client_secret')
                                    ->label('Facebook App Secret')
                                    ->password(),
                                TextInput::make('facebook_redirect_uri')
                                    ->label('OAuth Redirect URI')
                                    ->helperText('Contoh: https://domain.com/auth/facebook/callback'),
                            ]),

                        Tab::make('YouTube API')
                            ->icon('heroicon-o-play')
                            ->schema([
                                Placeholder::make('yt_guide')
                                    ->label('Petunjuk')
                                    ->content(new HtmlString('Aktifkan YouTube Data API v3 di <a href="https://console.cloud.google.com/" target="_blank" class="text-indigo-600 underline">Google Cloud Console</a>, lalu buat kredensial tipe Web Application.')),
                                Toggle::make('enable_youtube')
                                    ->label('Enable YouTube Integration')
                                    ->helperText('Izinkan pengguna untuk menghubungkan dan memposting ke YouTube.'),
                                TextInput::make('youtube_client_id')
                                    ->label('Google Client ID'),
                                TextInput::make('youtube_client_secret')
                                    ->label('Google Client Secret')
                                    ->password(),
                                TextInput::make('youtube_redirect_uri')
                                    ->label('OAuth Redirect URI'),
                            ]),

                        Tab::make('TikTok API')
                            ->icon('heroicon-o-musical-note')
                            ->schema([
                                Placeholder::make('tiktok_guide')
                                    ->label('Petunjuk')
                                    ->content(new HtmlString('Buat aplikasi di <a href="https://developers.tiktok.com/" target="_blank" class="text-indigo-600 underline">TikTok for Developers</a> dan request akses ke Content Posting API / Video Kit.')),
                                Toggle::make('enable_tiktok')
                                    ->label('Enable TikTok Integration')
                                    ->helperText('Izinkan pengguna untuk menghubungkan dan memposting ke TikTok.'),
                                TextInput::make('tiktok_client_key')
                                    ->label('TikTok Client Key'),
                                TextInput::make('tiktok_client_secret')
                                    ->label('TikTok Client Secret')
                                    ->password(),
                                TextInput::make('tiktok_redirect_uri')
                                    ->label('OAuth Redirect URI'),
                            ]),

                        Tab::make('Midtrans')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Placeholder::make('midtrans_guide')
                                    ->label('Petunjuk')
                                    ->content(new HtmlString('Dapatkan Key di menu Settings > Access Keys pada <a href="https://dashboard.midtrans.com/" target="_blank" class="text-indigo-600 underline">Dashboard Midtrans</a>.')),
                                TextInput::make('midtrans_server_key')
                                    ->label('Server Key')
                                    ->password(),
                                TextInput::make('midtrans_client_key')
                                    ->label('Client Key')
                                    ->password(),
                                Select::make('midtrans_is_production')
                                    ->label('Environment')
                                    ->options([
                                        '0' => 'Sandbox (Testing)',
                                        '1' => 'Production (Live)',
                                    ])
                                    ->required(),
                            ]),

                        Tab::make('Affiliate')
                            ->icon('heroicon-o-users')
                            ->schema([
                                TextInput::make('affiliate_cookie_days')
                                    ->label('Masa Aktif Cookie (Hari)')
                                    ->numeric()
                                    ->required()
                                    ->helperText('Berapa lama cookie afiliasi bertahan di browser pengguna setelah mengklik link (contoh: 30).'),
                            ]),
                    ])
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::setVal($key, $value);
        }

        Notification::make()
            ->title('Settings Saved')
            ->success()
            ->send();
    }
}
