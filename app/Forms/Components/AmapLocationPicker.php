<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class AmapLocationPicker extends Field
{
    protected string $view = 'filament.forms.components.amap-location-picker';

    protected string|Closure $addressField = 'address';

    protected string|Closure $latitudeField = 'latitude';

    protected string|Closure $longitudeField = 'longitude';

    protected string|Closure|null $height = '360px';

    protected int|Closure|null $zoom = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dehydrated(false);
        $this->label('地图选点');
        $this->helperText('搜索或点击地图选择院区位置，将自动回填地址与经纬度。');
    }

    public function addressField(string|Closure $field): static
    {
        $this->addressField = $field;

        return $this;
    }

    public function latitudeField(string|Closure $field): static
    {
        $this->latitudeField = $field;

        return $this;
    }

    public function longitudeField(string|Closure $field): static
    {
        $this->longitudeField = $field;

        return $this;
    }

    public function height(string|Closure|null $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function zoom(int|Closure|null $zoom): static
    {
        $this->zoom = $zoom;

        return $this;
    }

    public function getAddressField(): string
    {
        return (string) $this->evaluate($this->addressField);
    }

    public function getLatitudeField(): string
    {
        return (string) $this->evaluate($this->latitudeField);
    }

    public function getLongitudeField(): string
    {
        return (string) $this->evaluate($this->longitudeField);
    }

    public function getHeight(): string
    {
        return (string) ($this->evaluate($this->height) ?: '360px');
    }

    public function getZoom(): int
    {
        return (int) ($this->evaluate($this->zoom) ?: config('services.amap.default_zoom', 15));
    }

    public function getAddressStatePath(): string
    {
        return $this->resolveSiblingStatePath($this->getAddressField());
    }

    public function getLatitudeStatePath(): string
    {
        return $this->resolveSiblingStatePath($this->getLatitudeField());
    }

    public function getLongitudeStatePath(): string
    {
        return $this->resolveSiblingStatePath($this->getLongitudeField());
    }

    public function getWebKey(): ?string
    {
        $key = config('services.amap.web_key');

        return filled($key) ? (string) $key : null;
    }

    public function getSecurityJsCode(): ?string
    {
        $code = config('services.amap.security_js_code');

        return filled($code) ? (string) $code : null;
    }

    public function getDefaultLongitude(): float
    {
        return (float) config('services.amap.default_longitude', 116.397428);
    }

    public function getDefaultLatitude(): float
    {
        return (float) config('services.amap.default_latitude', 39.90923);
    }

    public function isConfigured(): bool
    {
        return filled($this->getWebKey());
    }

    protected function resolveSiblingStatePath(string $field): string
    {
        $parts = explode('.', (string) $this->getStatePath());
        array_pop($parts);
        $parts[] = $field;

        return implode('.', $parts);
    }
}
