import React from 'react';
import {
    Activity,
    BarChart3,
    Bot,
    CalendarDays,
    CheckCircle2,
    ClipboardList,
    FileText,
    FolderKanban,
    LockKeyhole,
    Mail,
    MailCheck,
    MessageSquareText,
    Milestone,
    Route,
    Send,
    ShieldCheck,
    Sparkles,
    TimerReset,
    UserCog,
    Users,
    Zap,
} from 'lucide-react';

const loginPath = '/login';
const contactPath = '/contact';
const supportEmail = 'support@projectmanager.local';

const features = [
    {
        title: 'Dashboard overview',
        copy: 'A clear operational view of active projects, pending work, task progress, and recent focus items.',
        icon: BarChart3,
    },
    {
        title: 'Project management',
        copy: 'Create, filter, and manage project portfolios with owners, members, priorities, status, and target dates.',
        icon: FolderKanban,
    },
    {
        title: 'Task management',
        copy: 'Track status, priority, due dates, estimated hours, spent hours, and assigned responsibility.',
        icon: ClipboardList,
    },
    {
        title: 'Collaboration',
        copy: 'Keep execution context close to the work with comments, subtasks, and supporting attachments.',
        icon: MessageSquareText,
    },
    {
        title: 'Milestones',
        copy: 'Structure delivery around project checkpoints, deadlines, completion state, and visible progress.',
        icon: Milestone,
    },
    {
        title: 'AI Work Report',
        copy: 'Generate a structured report from project and task data to summarize work and delivery status.',
        icon: Bot,
    },
];

const roles = [
    {
        name: 'Admin',
        icon: UserCog,
        summary: 'Full access across the workspace, including project control, audit visibility, and user-level administration.',
        points: ['Complete management permissions', 'Audit and security visibility', 'User and role control'],
    },
    {
        name: 'Project Manager',
        icon: FolderKanban,
        summary: 'Owns delivery coordination by creating projects, assigning team members, managing tasks, and viewing logs.',
        points: ['Create and manage projects', 'Assign members and tasks', 'Monitor project activity'],
    },
    {
        name: 'Team Member',
        icon: Users,
        summary: 'Works inside assigned projects with controlled access to update tasks and collaborate on delivery.',
        points: ['Access assigned projects', 'Update assigned tasks', 'Comment, add subtasks, and upload files'],
    },
];

const securityItems = [
    { label: 'Role-based access', icon: ShieldCheck },
    { label: 'Protected routes', icon: LockKeyhole },
    { label: 'CAPTCHA login', icon: CheckCircle2 },
    { label: 'Email-code verification', icon: MailCheck },
    { label: 'Request throttling', icon: TimerReset },
    { label: 'Session regeneration', icon: Route },
    { label: 'Audit traceability', icon: Activity },
];

const stats = [
    { number: '3', label: 'Access roles', icon: UserCog },
    { number: '6+', label: 'Core modules', icon: FolderKanban },
    { number: '7+', label: 'Security controls', icon: ShieldCheck },
    { number: 'AI', label: 'Powered reporting', icon: Sparkles },
];

const workflow = [
    'Organize projects',
    'Assign tasks',
    'Track progress',
    'Collaborate',
    'Monitor via audit log',
];

const navLinks = [
    { label: 'Features', href: '#features', isPage: false },
    { label: 'Roles', href: '#roles', isPage: false },
    { label: 'Security', href: '#security', isPage: false },
    { label: 'Modules', href: '#modules', isPage: false },
    { label: 'Contact', href: contactPath, isPage: true },
];

function scrollToSection(event, id) {
    event.preventDefault();
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function BrandMark({ size = 'h-10 w-10' }) {
    return (
        <div className={`brand-mark flex ${size} items-center justify-center rounded-lg text-white`}>
            <ClipboardList className="h-5 w-5" aria-hidden="true" />
        </div>
    );
}

function SectionHeader({ eyebrow, title, copy }) {
    return (
        <div className="mx-auto mb-10 max-w-3xl text-center">
            <span className="badge bg-cyan-100 text-cyan-800">{eyebrow}</span>
            <h2 className="mt-4 text-3xl font-bold text-slate-950 sm:text-4xl">{title}</h2>
            {copy && <p className="mt-4 text-sm leading-7 text-slate-600 sm:text-base">{copy}</p>}
        </div>
    );
}

function FeatureCard({ icon: Icon, title, copy }) {
    return (
        <article className="card h-full transition hover:-translate-y-0.5">
            <div className="icon-action mb-4" aria-hidden="true">
                <Icon className="h-5 w-5" />
            </div>
            <h3 className="text-lg font-bold text-slate-950">{title}</h3>
            <p className="mt-3 text-sm leading-6 text-slate-600">{copy}</p>
        </article>
    );
}

function RoleCard({ role }) {
    const Icon = role.icon;

    return (
        <article className="card h-full">
            <div className="mb-4 flex items-center gap-3">
                <div className="icon-action" aria-hidden="true">
                    <Icon className="h-5 w-5" />
                </div>
                <h3 className="text-xl font-bold text-slate-950">{role.name}</h3>
            </div>
            <p className="text-sm leading-6 text-slate-600">{role.summary}</p>
            <ul className="mt-5 space-y-3">
                {role.points.map((point) => (
                    <li key={point} className="flex gap-3 text-sm text-slate-600">
                        <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true" />
                        <span>{point}</span>
                    </li>
                ))}
            </ul>
        </article>
    );
}

export default function ProjectManagerLanding() {
    return (
        <div className="min-h-screen bg-transparent text-slate-900">
            {/* ── Navigation ── */}
            <header className="sticky top-0 z-50 border-b border-slate-200 bg-white/76 shadow-sm backdrop-blur-xl">
                <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="/" className="flex items-center gap-2.5" aria-label="ProjectManager home">
                        <BrandMark size="h-9 w-9" />
                        <span className="font-display hidden text-lg font-bold text-slate-900 sm:block">ProjectManager</span>
                    </a>
                    <nav className="hidden items-center gap-1 lg:flex" aria-label="Primary navigation">
                        {navLinks.map(({ label, href, isPage }) =>
                            isPage ? (
                                <a key={href} href={href} className="nav-link">
                                    {label}
                                </a>
                            ) : (
                                <a
                                    key={href}
                                    href={href}
                                    onClick={(e) => scrollToSection(e, href.slice(1))}
                                    className="nav-link"
                                >
                                    {label}
                                </a>
                            )
                        )}
                    </nav>
                    <a href={loginPath} className="btn-secondary">
                        Log In
                    </a>
                </div>
            </header>

            <main>
                {/* ── Hero ── */}
                <section className="px-4 pb-10 pt-12 sm:px-6 lg:px-8 lg:pb-16 lg:pt-20">
                    <div className="mx-auto max-w-3xl text-center">
                        <span className="badge bg-cyan-100 text-cyan-800">Internal project command center</span>
                        <h1 className="mt-5 text-4xl font-bold text-slate-950 sm:text-5xl lg:text-6xl">
                            <span className="gradient-text">Centralize</span> project planning, execution, and team visibility.
                        </h1>
                        <p className="mx-auto mt-5 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                            ProjectManager gives internal teams one structured workspace for projects, tasks, collaboration, audit activity, and secure access — all in one place.
                        </p>
                        <div className="mt-8 flex flex-wrap justify-center gap-3">
                            <a href={loginPath} className="btn-primary">
                                <Zap className="h-4 w-4" aria-hidden="true" />
                                Get Started
                            </a>
                            <a href={loginPath} className="btn-secondary">
                                Log In
                            </a>
                        </div>
                    </div>
                </section>

                {/* ── Stats strip ── */}
                <section className="px-4 py-6 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            {stats.map(({ number, label, icon: Icon }) => (
                                <div key={label} className="surface-row flex items-center gap-4 px-5 py-4">
                                    <div className="icon-action shrink-0" aria-hidden="true">
                                        <Icon className="h-4 w-4" />
                                    </div>
                                    <div>
                                        <p className="metric-number text-xl font-bold text-slate-950">{number}</p>
                                        <p className="mt-0.5 text-xs text-slate-500">{label}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Features ── */}
                <section id="features" className="px-4 py-16 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <SectionHeader
                            eyebrow="Key features"
                            title="Everything the team needs to move delivery forward."
                            copy="Built around the same internal workflows used by project managers, administrators, and delivery teams."
                        />
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            {features.map((feature) => (
                                <FeatureCard key={feature.title} {...feature} />
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Roles ── */}
                <section id="roles" className="px-4 py-16 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <SectionHeader
                            eyebrow="User roles"
                            title="Clear permissions for every internal responsibility."
                            copy="Access is structured around who manages delivery, who executes work, and who controls the system."
                        />
                        <div className="grid gap-4 lg:grid-cols-3">
                            {roles.map((role) => (
                                <RoleCard key={role.name} role={role} />
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Security ── */}
                <section id="security" className="px-4 py-16 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <div className="page-hero-panel overflow-hidden">
                            <div className="grid gap-8 p-6 lg:grid-cols-[0.85fr_1.15fr] lg:p-10">
                                <div>
                                    <span className="badge bg-emerald-100 text-emerald-800">Security</span>
                                    <h2 className="mt-4 text-3xl font-bold text-slate-950">Trust controls designed for internal access.</h2>
                                    <p className="mt-4 text-sm leading-7 text-slate-600">
                                        The application protects authentication, limits repeated requests, regenerates sessions after login, and records important actions for traceability.
                                    </p>
                                    <div className="mt-6">
                                        <a href={loginPath} className="btn-secondary">
                                            <ShieldCheck className="h-4 w-4" aria-hidden="true" />
                                            Access the workspace
                                        </a>
                                    </div>
                                </div>
                                <div className="grid gap-3 sm:grid-cols-2">
                                    {securityItems.map(({ label, icon: Icon }) => (
                                        <div key={label} className="surface-row flex items-center gap-3 p-4">
                                            <div className="icon-action !h-9 !w-9 shrink-0" aria-hidden="true">
                                                <Icon className="h-4 w-4" />
                                            </div>
                                            <span className="text-sm font-bold text-slate-800">{label}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* ── Modules / How it works ── */}
                <section id="modules" className="px-4 py-16 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <SectionHeader
                            eyebrow="How it works"
                            title="A simple workflow from planning to traceability."
                        />
                        <div className="grid gap-3 lg:grid-cols-5">
                            {workflow.map((step, index) => (
                                <div key={step} className="surface-row p-5">
                                    <p className="metric-number text-2xl text-cyan-700">0{index + 1}</p>
                                    <p className="mt-3 text-sm font-bold text-slate-900">{step}</p>
                                </div>
                            ))}
                        </div>

                        <div className="mt-8 grid gap-5 lg:grid-cols-[0.8fr_1.2fr] lg:items-stretch">
                            <div className="action-card card">
                                <div className="mb-5 flex items-center gap-3">
                                    <Sparkles className="h-6 w-6" aria-hidden="true" />
                                    <span className="badge bg-cyan-100 text-cyan-800">AI Work Report</span>
                                </div>
                                <h3 className="text-2xl font-bold text-white">Structured reporting from real project data.</h3>
                                <p className="mt-4 text-sm leading-7 text-slate-200">
                                    The AI Work Report summarizes assigned work, project context, task status, priorities, blockers, and progress into a professional report that can be reviewed or printed.
                                </p>
                            </div>
                            <div className="card">
                                <div className="mb-4 flex items-center gap-3">
                                    <div className="icon-action" aria-hidden="true">
                                        <FileText className="h-5 w-5" />
                                    </div>
                                    <h3 className="text-xl font-bold text-slate-950">Report-ready modules</h3>
                                </div>
                                <div className="grid gap-3 sm:grid-cols-2">
                                    {['Projects', 'Tasks', 'Milestones', 'Comments', 'Attachments', 'Audit logs'].map((module) => (
                                        <div key={module} className="surface-row px-4 py-3 text-sm font-bold text-slate-700">
                                            {module}
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* ── Contact CTA strip ── */}
                <section className="px-4 py-8 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <div className="surface-row flex flex-col gap-5 px-6 py-6 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex items-start gap-4">
                                <div className="icon-action !h-10 !w-10 shrink-0 mt-0.5" aria-hidden="true">
                                    <Mail className="h-5 w-5" />
                                </div>
                                <div>
                                    <p className="font-bold text-slate-950">Need access or support?</p>
                                    <p className="mt-1 text-sm text-slate-500">
                                        Contact the internal support team for workspace access, role provisioning, or project questions.
                                    </p>
                                </div>
                            </div>
                            <a href={contactPath} className="btn-primary shrink-0">
                                <Send className="h-4 w-4" aria-hidden="true" />
                                Contact support
                            </a>
                        </div>
                    </div>
                </section>

                {/* ── Final CTA ── */}
                <section className="px-4 py-12 sm:px-6 lg:px-8">
                    <div className="mx-auto max-w-7xl">
                        <div className="page-hero-panel flex flex-col gap-5 p-6 sm:p-10 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <span className="badge bg-cyan-100 text-cyan-800">Ready to start</span>
                                <h2 className="mt-4 text-3xl font-bold text-slate-950">Bring project work into one controlled workspace.</h2>
                                <p className="mt-3 text-sm leading-7 text-slate-600 max-w-xl">
                                    Projects, tasks, milestones, collaboration, audit logs, and AI-powered reporting — all secured behind role-based access control.
                                </p>
                            </div>
                            <a href={loginPath} className="btn-primary shrink-0">
                                <Zap className="h-4 w-4" aria-hidden="true" />
                                Get Started
                            </a>
                        </div>
                    </div>
                </section>
            </main>

            {/* ── Footer ── */}
            <footer className="border-t border-slate-200 px-4 py-10 sm:px-6 lg:px-8">
                <div className="mx-auto max-w-7xl">
                    <div className="flex flex-col gap-8 lg:flex-row lg:items-start lg:justify-between">
                        <div className="max-w-xs">
                            <div className="flex items-center gap-3">
                                <BrandMark size="h-9 w-9" />
                                <span className="font-display font-bold text-slate-900">ProjectManager</span>
                            </div>
                            <p className="mt-3 text-sm text-slate-500">Internal project command center. Not intended for public self-service access.</p>
                        </div>
                        <div className="flex flex-wrap gap-x-8 gap-y-3 text-sm text-slate-500">
                            <a href="#features" onClick={(e) => scrollToSection(e, 'features')} className="hover:text-slate-900 transition-colors">Features</a>
                            <a href="#roles" onClick={(e) => scrollToSection(e, 'roles')} className="hover:text-slate-900 transition-colors">Roles</a>
                            <a href="#security" onClick={(e) => scrollToSection(e, 'security')} className="hover:text-slate-900 transition-colors">Security</a>
                            <a href="#modules" onClick={(e) => scrollToSection(e, 'modules')} className="hover:text-slate-900 transition-colors">Modules</a>
                            <a href={contactPath} className="hover:text-slate-900 transition-colors">Contact</a>
                            <a href={`mailto:${supportEmail}`} className="hover:text-slate-900 transition-colors">{supportEmail}</a>
                        </div>
                        <p className="text-sm text-slate-400 lg:shrink-0">&copy; {new Date().getFullYear()} ProjectManager.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
