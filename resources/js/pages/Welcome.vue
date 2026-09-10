<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps<{ name?: string }>();

const isModalOpen = ref(false);
const submitted = ref(false);
const form = useForm({
    name: '',
    contact: '',
    organization_name: '',
    organization_type: '',
    request_type: 'consultation',
    requirements: '',
    consent: false,
    website: '',
});
const modalTitle = computed(() =>
    form.request_type === 'trial' ? '申请产品试用' : '预约产品咨询',
);
const capabilities = [
    ['患者建档', '统一管理患者与家庭成员档案，沉淀完整服务记录。'],
    ['智能接诊', '候诊、问诊、病历一体协同，让医生专注诊疗本身。'],
    ['电子病历', '结构化中西医病历模板，历史记录随时调阅。'],
    ['处方发药', '开方、审方、收费、发药形成闭环，减少人工差错。'],
    ['药房库存', '批次、效期和库存流水清晰可追踪，及时预警。'],
    ['经营对账', '收费、退款、日结与门店经营数据自动汇总。'],
];
const workflow = [
    '患者建档',
    '接诊病历',
    '医生开方',
    '收费结算',
    '药房发药',
    '库存扣减',
];

const openConsultation = (type: 'consultation' | 'trial') => {
    form.request_type = type;
    form.clearErrors();
    submitted.value = false;
    isModalOpen.value = true;
    document.body.style.overflow = 'hidden';
};
const closeModal = () => {
    isModalOpen.value = false;
    document.body.style.overflow = '';
};
const submit = () =>
    form.post('/consultations', {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true;
            form.reset(
                'name',
                'contact',
                'organization_name',
                'organization_type',
                'requirements',
                'consent',
            );
        },
    });
</script>

<template>
    <Head title="诊所数字化经营系统">
        <meta
            name="description"
            content="面向中医馆、社区诊所与连锁门诊的云端 SaaS 经营系统，覆盖患者、接诊、病历、处方、收费、药房与库存。"
        />
    </Head>
    <div class="min-h-screen bg-[#f5f8f7] text-[#14231f]">
        <header
            class="fixed inset-x-0 top-0 z-40 border-b border-white/70 bg-white/85 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-18 max-w-7xl items-center justify-between px-5 lg:px-8"
            >
                <a
                    href="#top"
                    class="flex items-center gap-3 font-semibold tracking-tight"
                >
                    <span
                        class="grid h-9 w-9 place-items-center rounded-xl bg-[#087b61] text-white shadow-lg shadow-emerald-900/15"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 3v18M3 12h18" stroke-linecap="round" />
                            <path
                                d="M6.5 6.5c3.5 0 5.5 2 5.5 5.5-3.5 0-5.5-2-5.5-5.5Z"
                            />
                        </svg>
                    </span>
                    <span>{{ name || '彩云诊所云' }}</span>
                </a>
                <nav
                    class="hidden items-center gap-8 text-sm text-slate-600 md:flex"
                >
                    <a href="#product" class="hover:text-[#087b61]">产品能力</a
                    ><a href="#workflow" class="hover:text-[#087b61]"
                        >业务闭环</a
                    ><a href="#value" class="hover:text-[#087b61]">产品价值</a>
                </nav>
                <button
                    class="rounded-full bg-[#087b61] px-5 py-2.5 text-sm font-medium text-white hover:bg-[#066650]"
                    @click="openConsultation('consultation')"
                >
                    预约咨询
                </button>
            </div>
        </header>

        <main id="top">
            <section
                class="relative overflow-hidden px-5 pt-34 pb-20 lg:px-8 lg:pt-44 lg:pb-30"
            >
                <div
                    class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_78%_20%,rgba(68,207,158,.18),transparent_30%),radial-gradient(circle_at_10%_80%,rgba(33,145,124,.10),transparent_34%)]"
                />
                <div
                    class="mx-auto grid max-w-7xl items-center gap-14 lg:grid-cols-[1.05fr_.95fr]"
                >
                    <div>
                        <div
                            class="mb-6 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-800"
                        >
                            <span
                                class="h-2 w-2 rounded-full bg-emerald-500"
                            />专为诊所打造的云端经营系统
                        </div>
                        <h1
                            class="max-w-3xl text-4xl leading-[1.12] font-semibold tracking-[-0.04em] text-[#102c25] sm:text-5xl lg:text-7xl"
                        >
                            让每一家诊所，<br />经营更<span
                                class="text-[#087b61]"
                                >简单高效</span
                            >
                        </h1>
                        <p
                            class="mt-7 max-w-2xl text-lg leading-8 text-slate-600"
                        >
                            从患者建档、接诊病历到处方收费、药房库存，用一套系统连接诊疗与经营，让中医馆和社区诊所轻松完成数字化升级。
                        </p>
                        <div class="mt-9 flex flex-wrap gap-4">
                            <button
                                class="rounded-full bg-[#087b61] px-7 py-3.5 font-medium text-white shadow-xl shadow-emerald-900/15 transition hover:-translate-y-0.5 hover:bg-[#066650]"
                                @click="openConsultation('trial')"
                            >
                                申请试用 <span class="ml-2">→</span>
                            </button>
                            <button
                                class="rounded-full border border-slate-300 bg-white px-7 py-3.5 font-medium text-slate-700 hover:border-emerald-600 hover:text-emerald-700"
                                @click="openConsultation('consultation')"
                            >
                                预约咨询
                            </button>
                        </div>
                        <div
                            class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm text-slate-500"
                        >
                            <span>✓ 无需本地服务器</span
                            ><span>✓ 数据安全隔离</span
                            ><span>✓ 持续免费升级</span>
                        </div>
                    </div>
                    <div class="relative mx-auto w-full max-w-xl">
                        <div
                            class="absolute -inset-6 rounded-[2.5rem] bg-gradient-to-br from-emerald-200/70 to-cyan-100/40 blur-2xl"
                        />
                        <div
                            class="relative overflow-hidden rounded-[2rem] border border-white/80 bg-[#102d27] p-5 shadow-2xl shadow-emerald-950/20 sm:p-7"
                        >
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-emerald-200">
                                        今日门诊
                                    </p>
                                    <p
                                        class="mt-1 text-xl font-semibold text-white"
                                    >
                                        诊所经营概览
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-emerald-400/15 px-3 py-1 text-xs text-emerald-200"
                                    >实时更新</span
                                >
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div
                                    v-for="item in [
                                        ['候诊', '12'],
                                        ['已接诊', '38'],
                                        ['今日营收', '¥8,620'],
                                    ]"
                                    :key="item[0]"
                                    class="rounded-2xl bg-white/8 p-4"
                                >
                                    <p class="text-xs text-emerald-100/65">
                                        {{ item[0] }}
                                    </p>
                                    <p
                                        class="mt-2 text-lg font-semibold text-white sm:text-2xl"
                                    >
                                        {{ item[1] }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 rounded-2xl bg-white p-5">
                                <div class="flex items-center justify-between">
                                    <p class="font-medium text-slate-800">
                                        接诊进度
                                    </p>
                                    <span class="text-xs text-emerald-700"
                                        >查看全部</span
                                    >
                                </div>
                                <div
                                    v-for="(patient, index) in [
                                        ['08', '李女士', '候诊中'],
                                        ['09', '王先生', '接诊中'],
                                        ['10', '张女士', '待收费'],
                                    ]"
                                    :key="patient[0]"
                                    class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-4"
                                >
                                    <span
                                        class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-sm font-semibold text-emerald-700"
                                        >{{ patient[0] }}</span
                                    ><span
                                        class="flex-1 text-sm font-medium text-slate-700"
                                        >{{ patient[1] }}</span
                                    ><span
                                        :class="
                                            index === 1
                                                ? 'bg-emerald-100 text-emerald-700'
                                                : 'bg-slate-100 text-slate-500'
                                        "
                                        class="rounded-full px-3 py-1 text-xs"
                                        >{{ patient[2] }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="product" class="bg-white px-5 py-22 lg:px-8 lg:py-28">
                <div class="mx-auto max-w-7xl">
                    <div class="max-w-2xl">
                        <p
                            class="text-sm font-semibold tracking-[.2em] text-[#087b61]"
                        >
                            CORE CAPABILITIES
                        </p>
                        <h2
                            class="mt-4 text-3xl font-semibold tracking-tight sm:text-5xl"
                        >
                            一套系统，覆盖诊所经营全流程
                        </h2>
                        <p class="mt-5 leading-7 text-slate-600">
                            围绕真实门诊场景设计，每个角色都有清晰、高效的工作台。
                        </p>
                    </div>
                    <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="(item, index) in capabilities"
                            :key="item[0]"
                            class="group rounded-3xl border border-slate-200 bg-[#fbfdfc] p-7 transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-xl hover:shadow-emerald-950/5"
                        >
                            <span
                                class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-100 font-semibold text-emerald-700"
                                >0{{ index + 1 }}</span
                            >
                            <h3 class="mt-6 text-xl font-semibold">
                                {{ item[0] }}
                            </h3>
                            <p class="mt-3 leading-7 text-slate-600">
                                {{ item[1] }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <section id="workflow" class="px-5 py-22 lg:px-8 lg:py-28">
                <div
                    class="mx-auto max-w-7xl rounded-[2rem] bg-[#102d27] px-6 py-12 text-white sm:px-10 lg:px-16 lg:py-18"
                >
                    <div class="max-w-2xl">
                        <p
                            class="text-sm font-semibold tracking-[.2em] text-emerald-300"
                        >
                            CLOSED LOOP
                        </p>
                        <h2 class="mt-4 text-3xl font-semibold sm:text-5xl">
                            从接诊到发药，完整业务闭环
                        </h2>
                    </div>
                    <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
                        <div
                            v-for="(item, index) in workflow"
                            :key="item"
                            class="relative rounded-2xl border border-white/10 bg-white/6 p-5"
                        >
                            <span class="text-xs text-emerald-300"
                                >STEP {{ index + 1 }}</span
                            >
                            <p class="mt-4 font-medium">{{ item }}</p>
                            <span
                                v-if="index < workflow.length - 1"
                                class="absolute top-1/2 -right-3 z-10 hidden text-emerald-400 lg:block"
                                >→</span
                            >
                        </div>
                    </div>
                </div>
            </section>

            <section id="value" class="bg-white px-5 py-22 lg:px-8 lg:py-28">
                <div
                    class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-2 lg:items-center"
                >
                    <div>
                        <p
                            class="text-sm font-semibold tracking-[.2em] text-[#087b61]"
                        >
                            BUILT FOR CLINICS
                        </p>
                        <h2
                            class="mt-4 text-3xl font-semibold tracking-tight sm:text-5xl"
                        >
                            业务在线，数据清晰，管理更从容
                        </h2>
                        <p
                            class="mt-6 max-w-xl text-lg leading-8 text-slate-600"
                        >
                            支持单店和连锁机构统一运营，成员按角色授权，租户数据严格隔离，为未来扩张留足空间。
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div
                            v-for="item in [
                                ['多门店', '统一管理'],
                                ['多角色', '精细权限'],
                                ['云端化', '随时访问'],
                                ['可追溯', '操作留痕'],
                            ]"
                            :key="item[0]"
                            class="rounded-3xl bg-[#f2f8f6] p-7"
                        >
                            <p class="text-2xl font-semibold text-[#087b61]">
                                {{ item[0] }}
                            </p>
                            <p class="mt-2 text-slate-500">{{ item[1] }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="px-5 py-16 lg:px-8 lg:py-22">
                <div
                    class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-8 rounded-[2rem] bg-gradient-to-r from-[#087b61] to-[#075b4a] px-8 py-12 text-white md:flex-row md:items-center lg:px-14"
                >
                    <div>
                        <h2 class="text-3xl font-semibold">
                            准备好升级你的诊所了吗？
                        </h2>
                        <p class="mt-3 text-emerald-50/80">
                            留下联系方式，我们将为你提供一对一产品演示。
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button
                            class="rounded-full bg-white px-6 py-3 font-medium text-emerald-800"
                            @click="openConsultation('trial')"
                        >
                            申请试用</button
                        ><button
                            class="rounded-full border border-white/40 px-6 py-3 font-medium"
                            @click="openConsultation('consultation')"
                        >
                            预约咨询
                        </button>
                    </div>
                </div>
            </section>
        </main>

        <footer
            class="border-t border-slate-200 bg-white px-5 py-8 text-sm text-slate-500 lg:px-8"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col justify-between gap-3 sm:flex-row"
            >
                <span>{{ name || '彩云诊所云' }} · 诊所数字化经营系统</span
                ><span>© {{ new Date().getFullYear() }} 保留所有权利</span>
            </div>
        </footer>

        <Transition name="modal"
            ><div
                v-if="isModalOpen"
                class="fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-slate-950/55 p-4 backdrop-blur-sm"
                role="dialog"
                aria-modal="true"
                @click.self="closeModal"
            >
                <div
                    class="my-6 w-full max-w-2xl overflow-hidden rounded-[1.75rem] bg-white shadow-2xl"
                >
                    <div
                        class="flex items-start justify-between border-b border-slate-100 px-6 py-5 sm:px-8"
                    >
                        <div>
                            <h2 class="text-2xl font-semibold">
                                {{ modalTitle }}
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                请留下联系方式和简单需求，我们会尽快与你联系。
                            </p>
                        </div>
                        <button
                            class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 text-xl text-slate-500 hover:bg-slate-200"
                            aria-label="关闭"
                            @click="closeModal"
                        >
                            ×
                        </button>
                    </div>
                    <div v-if="submitted" class="px-8 py-14 text-center">
                        <span
                            class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-emerald-100 text-3xl text-emerald-700"
                            >✓</span
                        >
                        <h3 class="mt-5 text-2xl font-semibold">提交成功</h3>
                        <p class="mt-2 text-slate-500">
                            感谢你的关注，我们会尽快与你取得联系。
                        </p>
                        <button
                            class="mt-7 rounded-full bg-[#087b61] px-7 py-3 font-medium text-white"
                            @click="closeModal"
                        >
                            完成
                        </button>
                    </div>
                    <form
                        v-else
                        class="grid gap-5 px-6 py-6 sm:grid-cols-2 sm:px-8 sm:py-8"
                        @submit.prevent="submit"
                    >
                        <label class="form-field"
                            ><span>姓名 *</span
                            ><input
                                v-model="form.name"
                                type="text"
                                placeholder="怎么称呼您"
                            /><small v-if="form.errors.name">{{
                                form.errors.name
                            }}</small></label
                        ><label class="form-field"
                            ><span>联系方式 *</span
                            ><input
                                v-model="form.contact"
                                type="text"
                                placeholder="手机、邮箱或微信"
                            /><small v-if="form.errors.contact">{{
                                form.errors.contact
                            }}</small></label
                        ><label class="form-field"
                            ><span>机构名称</span
                            ><input
                                v-model="form.organization_name"
                                type="text"
                                placeholder="诊所或团队名称" /></label
                        ><label class="form-field"
                            ><span>机构类型</span
                            ><select v-model="form.organization_type">
                                <option value="">请选择</option>
                                <option value="tcm_clinic">中医馆</option>
                                <option value="community_clinic">
                                    社区诊所
                                </option>
                                <option value="general_clinic">综合诊所</option>
                                <option value="chain_clinic">连锁诊所</option>
                                <option value="other">其他</option>
                            </select></label
                        ><label class="form-field sm:col-span-2"
                            ><span>需求说明</span
                            ><textarea
                                v-model="form.requirements"
                                rows="4"
                                placeholder="请简单描述门店数量、当前痛点或期望上线时间"
                            /><small v-if="form.errors.requirements">{{
                                form.errors.requirements
                            }}</small></label
                        ><label class="hidden"
                            ><input
                                v-model="form.website"
                                tabindex="-1"
                                autocomplete="off" /></label
                        ><label
                            class="flex items-start gap-2 text-sm text-slate-500 sm:col-span-2"
                            ><input
                                v-model="form.consent"
                                type="checkbox"
                                class="mt-1 accent-emerald-700"
                            /><span
                                >我同意平台为产品咨询目的保存并使用以上信息。</span
                            ></label
                        ><small
                            v-if="form.errors.consent"
                            class="-mt-3 text-red-500 sm:col-span-2"
                            >请先同意信息使用说明</small
                        ><button
                            type="submit"
                            class="rounded-xl bg-[#087b61] px-6 py-3.5 font-medium text-white hover:bg-[#066650] disabled:opacity-60 sm:col-span-2"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? '正在提交…' : '提交信息' }}
                        </button>
                    </form>
                </div>
            </div></Transition
        >
    </div>
</template>

<style scoped>
.form-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #334155;
}
.form-field input,
.form-field select,
.form-field textarea {
    width: 100%;
    border: 1px solid #dbe3e0;
    border-radius: 0.8rem;
    background: #f8faf9;
    padding: 0.8rem 0.95rem;
    font-weight: 400;
    outline: none;
    transition: 0.2s;
}
.form-field input:focus,
.form-field select:focus,
.form-field textarea:focus {
    border-color: #0a8a6b;
    background: white;
    box-shadow: 0 0 0 3px rgba(10, 138, 107, 0.1);
}
.form-field small {
    color: #ef4444;
    font-weight: 400;
}
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}
.modal-enter-active > div,
.modal-leave-active > div {
    transition: transform 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
.modal-enter-from > div,
.modal-leave-to > div {
    transform: translateY(16px) scale(0.98);
}
</style>
