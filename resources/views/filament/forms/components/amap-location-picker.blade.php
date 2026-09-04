@php
    $isConfigured = $isConfigured();
    $webKey = $getWebKey();
    $securityJsCode = $getSecurityJsCode();
    $mapHeight = $getHeight();
    $zoom = $getZoom();
    $defaultLng = $getDefaultLongitude();
    $defaultLat = $getDefaultLatitude();
    $addressStatePath = $getAddressStatePath();
    $latitudeStatePath = $getLatitudeStatePath();
    $longitudeStatePath = $getLongitudeStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @unless ($isConfigured)
        <div class="rounded-lg border border-warning-300 bg-warning-50 px-4 py-3 text-sm text-warning-800 dark:border-warning-600 dark:bg-warning-950 dark:text-warning-200">
            尚未配置高德地图。请在 <code>.env</code> 中设置 <code>AMAP_WEB_KEY</code>
            @if (! filled($securityJsCode))
                与 <code>AMAP_SECURITY_JS_CODE</code>
            @endif
            后刷新页面。
        </div>
    @else
        <div
            wire:ignore
            x-data="amapLocationPicker({
                key: @js($webKey),
                securityJsCode: @js($securityJsCode),
                zoom: {{ $zoom }},
                defaultLng: {{ $defaultLng }},
                defaultLat: {{ $defaultLat }},
                addressPath: @js($addressStatePath),
                latitudePath: @js($latitudeStatePath),
                longitudePath: @js($longitudeStatePath),
            })"
            x-init="init()"
            class="space-y-3"
        >
            <div class="flex gap-2">
                <input
                    type="search"
                    x-model="keyword"
                    @keydown.enter.prevent="search()"
                    placeholder="搜索地址 / 医院 / 地点"
                    class="fi-input block w-full rounded-lg border-none bg-white px-3 py-2 text-sm shadow-sm ring-1 ring-gray-950/10 transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-600 dark:bg-white/5 dark:text-white dark:ring-white/20 dark:placeholder:text-gray-500 dark:focus:ring-primary-500"
                />
                <button
                    type="button"
                    @click="search()"
                    class="fi-btn relative grid-flow-col items-center justify-center gap-1.5 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm outline-none transition hover:bg-primary-500 focus-visible:ring-2 focus-visible:ring-primary-500/50"
                >
                    搜索
                </button>
            </div>

            <div
                x-ref="map"
                class="w-full overflow-hidden rounded-lg border border-gray-200 dark:border-white/10"
                style="height: {{ $mapHeight }}"
            ></div>

            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="hint"></p>
        </div>
    @endunless
</x-dynamic-component>

@once
    @script
    <script>
        window.__amapLoaderPromise = window.__amapLoaderPromise || null;

        window.loadAmapSdk = function loadAmapSdk(key, securityJsCode) {
            if (window.AMap) {
                return Promise.resolve(window.AMap);
            }

            if (window.__amapLoaderPromise) {
                return window.__amapLoaderPromise;
            }

            window.__amapLoaderPromise = new Promise((resolve, reject) => {
                if (securityJsCode) {
                    window._AMapSecurityConfig = {
                        securityJsCode: securityJsCode,
                    };
                }

                const script = document.createElement('script');
                script.src = `https://webapi.amap.com/maps?v=2.0&key=${encodeURIComponent(key)}&plugin=AMap.PlaceSearch,AMap.Geocoder,AMap.AutoComplete`;
                script.async = true;
                script.onload = () => {
                    if (window.AMap) {
                        resolve(window.AMap);
                    } else {
                        reject(new Error('AMap SDK loaded but window.AMap is missing'));
                    }
                };
                script.onerror = () => reject(new Error('Failed to load AMap SDK'));
                document.head.appendChild(script);
            });

            return window.__amapLoaderPromise;
        };

        Alpine.data('amapLocationPicker', (config) => ({
            keyword: '',
            hint: '点击地图选点，或上方搜索后定位。',
            map: null,
            marker: null,
            geocoder: null,
            placeSearch: null,

            async init() {
                try {
                    const AMap = await window.loadAmapSdk(config.key, config.securityJsCode);
                    await this.$nextTick();
                    this.bootMap(AMap);
                } catch (error) {
                    console.error(error);
                    this.hint = '高德地图加载失败，请检查 Key / 安全密钥与域名白名单。';
                }
            },

            bootMap(AMap) {
                const initialLng = this.toNumber(this.$wire.get(config.longitudePath)) ?? config.defaultLng;
                const initialLat = this.toNumber(this.$wire.get(config.latitudePath)) ?? config.defaultLat;
                const hasPoint = this.toNumber(this.$wire.get(config.longitudePath)) !== null
                    && this.toNumber(this.$wire.get(config.latitudePath)) !== null;

                this.map = new AMap.Map(this.$refs.map, {
                    zoom: config.zoom,
                    center: [initialLng, initialLat],
                    viewMode: '2D',
                });

                this.geocoder = new AMap.Geocoder({ radius: 1000, extensions: 'base' });
                this.placeSearch = new AMap.PlaceSearch({
                    map: this.map,
                    pageSize: 1,
                    citylimit: false,
                });

                if (hasPoint) {
                    this.setMarker(initialLng, initialLat, { reverseGeocode: false });
                }

                this.map.on('click', (event) => {
                    const lng = event.lnglat.getLng();
                    const lat = event.lnglat.getLat();
                    this.setMarker(lng, lat, { reverseGeocode: true });
                });
            },

            setMarker(lng, lat, { reverseGeocode = true } = {}) {
                const AMap = window.AMap;
                const position = [lng, lat];

                if (! this.marker) {
                    this.marker = new AMap.Marker({
                        position,
                        draggable: true,
                        cursor: 'move',
                    });
                    this.marker.setMap(this.map);
                    this.marker.on('dragend', (event) => {
                        const next = event.target.getPosition();
                        this.applyCoordinates(next.getLng(), next.getLat(), true);
                    });
                } else {
                    this.marker.setPosition(position);
                }

                this.map.setCenter(position);
                this.applyCoordinates(lng, lat, reverseGeocode);
            },

            applyCoordinates(lng, lat, reverseGeocode) {
                const longitude = Number(lng).toFixed(7);
                const latitude = Number(lat).toFixed(7);

                this.$wire.set(config.longitudePath, longitude, false);
                this.$wire.set(config.latitudePath, latitude, false);

                if (! reverseGeocode || ! this.geocoder) {
                    this.hint = `已选坐标：${longitude}, ${latitude}`;
                    return;
                }

                this.hint = '正在解析地址…';
                this.geocoder.getAddress([lng, lat], (status, result) => {
                    if (status === 'complete' && result?.regeocode?.formattedAddress) {
                        const address = result.regeocode.formattedAddress;
                        this.$wire.set(config.addressPath, address, false);
                        this.hint = `已选点：${address}`;
                        return;
                    }

                    this.hint = `已选坐标：${longitude}, ${latitude}（地址解析失败，可手动填写）`;
                });
            },

            search() {
                const keyword = (this.keyword || '').trim();
                if (! keyword || ! this.placeSearch) {
                    return;
                }

                this.hint = `正在搜索「${keyword}」…`;
                this.placeSearch.search(keyword, (status, result) => {
                    if (status !== 'complete' || ! result?.poiList?.pois?.length) {
                        this.hint = `未找到「${keyword}」，请换个关键词或直接点击地图。`;
                        return;
                    }

                    const poi = result.poiList.pois[0];
                    const lng = poi.location.lng;
                    const lat = poi.location.lat;
                    const address = [poi.pname, poi.cityname, poi.adname, poi.address, poi.name]
                        .filter(Boolean)
                        .filter((part, index, arr) => arr.indexOf(part) === index)
                        .join('');

                    this.setMarker(lng, lat, { reverseGeocode: false });
                    this.$wire.set(config.addressPath, address || poi.name, false);
                    this.hint = `已定位：${address || poi.name}`;
                });
            },

            toNumber(value) {
                if (value === null || value === undefined || value === '') {
                    return null;
                }

                const number = Number(value);
                return Number.isFinite(number) ? number : null;
            },
        }));
    </script>
    @endscript
@endonce
