import React from 'react';
import { createRoot } from 'react-dom/client';
import ProjectManagerLanding from './components/ProjectManagerLanding.jsx';
import ContactPage from './components/ContactPage.jsx';
import {
    Activity,
    AlertTriangle,
    ArrowUpRight,
    BarChart3,
    CalendarClock,
    CheckCircle2,
    CircleDot,
    ClipboardList,
    Copy,
    Eye,
    Clock3,
    FileText,
    FolderKanban,
    LayoutDashboard,
    ListChecks,
    Plus,
    Printer,
    Search,
    ShieldCheck,
    Sparkles,
    Moon,
    Sun,
    Target,
    Users,
} from 'lucide-react';

const statusLabels = {
    pending: 'Pending',
    in_progress: 'In progress',
    completed: 'Completed',
    todo: 'To do',
    done: 'Done',
};

const statusTone = {
    pending: 'bg-amber-50 text-amber-700 ring-amber-200',
    in_progress: 'bg-blue-50 text-blue-700 ring-blue-200',
    completed: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    todo: 'bg-slate-100 text-slate-700 ring-slate-200',
    done: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
};

const priorityTone = {
    low: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    medium: 'bg-amber-50 text-amber-700 ring-amber-200',
    high: 'bg-rose-50 text-rose-700 ring-rose-200',
};

function cx(...classes) {
    return classes.filter(Boolean).join(' ');
}

function formatRole(role) {
    return String(role || 'User').replaceAll('_', ' ');
}

function initials(name) {
    return String(name || 'U')
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
}

function MetricCard({ icon: Icon, label, value, helper, tone = 'text-slate-900' }) {
    return (
        <div className="surface-row px-4 py-4">
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="text-xs font-medium uppercase tracking-wide text-slate-500">{label}</p>
                    <p className={cx('metric-number mt-2 text-2xl font-semibold', tone)}>{value}</p>
                </div>
                <div className="icon-action !h-9 !w-9">
                    <Icon size={18} />
                </div>
            </div>
            <p className="mt-3 text-xs text-slate-500">{helper}</p>
        </div>
    );
}

function StatusBadge({ value, priority = false }) {
    return (
        <span className={cx(
            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset',
            priority ? priorityTone[value] : statusTone[value],
        )}>
            {priority ? value : statusLabels[value] || value}
        </span>
    );
}

function ProjectRow({ project }) {
    const progress = project.tasksTotal > 0
        ? Math.round((project.tasksDone / project.tasksTotal) * 100)
        : 0;

    return (
        <a href={project.url} className="block border-b border-slate-100 px-4 py-4 transition hover:bg-cyan-50/50">
            <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div className="min-w-0">
                    <div className="flex flex-wrap items-center gap-2">
                        <h3 className="truncate text-sm font-semibold text-slate-950">{project.name}</h3>
                        <StatusBadge value={project.status} />
                    </div>
                    <p className="mt-1 line-clamp-1 text-sm text-slate-500">{project.description || 'No description'}</p>
                </div>
                <div className="flex min-w-64 items-center gap-4">
                    <div className="flex-1">
                        <div className="mb-1 flex items-center justify-between text-xs text-slate-500">
                            <span>{project.tasksDone}/{project.tasksTotal} tasks</span>
                            <span>{progress}%</span>
                        </div>
                        <div className="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div className="h-full rounded-full bg-cyan-700" style={{ width: `${progress}%` }} />
                        </div>
                    </div>
                    <div className="flex -space-x-2">
                        {[project.owner, ...project.members].filter(Boolean).slice(0, 3).map((name) => (
                            <span key={name} className="avatar-mark flex h-8 w-8 items-center justify-center rounded-full border-2 border-white text-xs font-semibold">
                                {initials(name)}
                            </span>
                        ))}
                    </div>
                </div>
            </div>
        </a>
    );
}

function TaskItem({ task }) {
    return (
        <a href={task.url} className="surface-row mb-2 flex items-start justify-between gap-3 px-3 py-3">
            <div className="flex min-w-0 gap-3">
                <div className={cx(
                    'mt-0.5 flex h-7 w-7 items-center justify-center rounded-md',
                    task.status === 'done' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600',
                )}>
                    {task.status === 'done' ? <CheckCircle2 size={16} /> : <CircleDot size={16} />}
                </div>
                <div className="min-w-0">
                    <p className="truncate text-sm font-medium text-slate-950">{task.title}</p>
                    <p className="mt-0.5 text-xs text-slate-500">
                        {task.project}{task.assignee ? ` - ${task.assignee}` : ''}
                    </p>
                </div>
            </div>
            <div className="flex shrink-0 items-center gap-2">
                {task.dueDate && (
                    <span className={cx('text-xs', task.isOverdue ? 'text-rose-600' : 'text-slate-500')}>
                        {task.dueDate}
                    </span>
                )}
                <span className="text-xs font-medium text-slate-400">{task.actionLabel}</span>
                <StatusBadge value={task.priority} priority />
            </div>
        </a>
    );
}

function ActivityItem({ log }) {
    const content = (
        <>
            <div className="icon-action !h-8 !w-8 shrink-0">
                <Activity size={15} />
            </div>
            <div className="min-w-0">
                <p className="text-sm text-slate-700">{log.description}</p>
                <p className="mt-0.5 text-xs text-slate-400">{log.user} - {log.time}</p>
            </div>
        </>
    );

    return log.url ? (
        <a href={log.url} className="surface-row mb-2 flex gap-3 px-3 py-3">
            {content}
        </a>
    ) : (
        <div className="surface-row mb-2 flex gap-3 px-3 py-3">
            {content}
        </div>
    );
}

const urgencyTone = {
    high: 'bg-rose-50 text-rose-700 ring-rose-200',
    medium: 'bg-amber-50 text-amber-700 ring-amber-200',
    low: 'bg-emerald-50 text-emerald-700 ring-emerald-200',
};

function UrgencyBadge({ value }) {
    const label = value ? `${value} urgency` : 'priority';

    return (
        <span className={cx(
            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium capitalize ring-1 ring-inset',
            urgencyTone[value] || urgencyTone.low,
        )}>
            {label}
        </span>
    );
}

function formatReportDate(value) {
    const date = new Date(value);

    if (!value || Number.isNaN(date.getTime())) {
        return 'Not generated';
    }

    return new Intl.DateTimeFormat(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function reportSourceLabel(report) {
    if (!report) {
        return 'Not generated';
    }

    if (report.source === 'groq') {
        return `Groq Llama${report.model ? ` - ${report.model}` : ''}`;
    }

    return report.provider === 'local' ? 'Priority engine' : 'Fallback analysis';
}

function ReportMetaItem({ label, value }) {
    return (
        <div className="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
            <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{label}</p>
            <p className="mt-1 text-sm font-medium leading-5 text-slate-950">{value || 'None'}</p>
        </div>
    );
}

function ReportMetric({ label, value }) {
    return (
        <div className="rounded-md border border-slate-200 bg-white px-3 py-2">
            <p className="metric-number text-xl font-semibold text-slate-950">{value}</p>
            <p className="mt-0.5 text-[11px] font-medium uppercase tracking-wide text-slate-500">{label}</p>
        </div>
    );
}

function buildReportText(report, user, metrics, nextActions) {
    const lines = [
        `Professional Work Report - ${user?.name || 'Team member'}`,
        `Role: ${formatRole(user?.role)}`,
        `Generated on: ${formatReportDate(report.generated_at)}`,
        `Source: ${reportSourceLabel(report)}`,
        '',
        'Executive Summary',
        report.summary,
        report.focus,
        '',
        'Metrics',
        `Recommended tasks: ${metrics.recommended}`,
        `High urgency: ${metrics.highUrgency}`,
        `Overdue: ${metrics.overdue}`,
        `Blocked: ${metrics.blocked}`,
        '',
        'Priority Work',
        ...(report.tasks ?? []).flatMap((task, index) => [
            `${index + 1}. ${task.title} (${task.project})`,
            `   Status: ${statusLabels[task.status] || task.status}; Priority: ${task.priority}; Urgency: ${task.urgency}`,
            `   Reason: ${task.why}`,
            `   Recommended action: ${task.recommended_action}`,
        ]),
        '',
        'Risks And Watch List',
        ...(report.blockers?.length ? report.blockers.map((blocker) => `- ${blocker}`) : ['No immediate blockers detected.']),
        '',
        'Recommended Next Steps',
        ...(nextActions.length ? nextActions.map((action, index) => `${index + 1}. ${action}`) : ['No next action required.']),
    ];

    return lines.join('\n');
}

function ReportSection({ icon: Icon, title, children }) {
    return (
        <section className="surface-row px-3 py-3">
            <div className="mb-3 flex items-center gap-2 text-xs font-semibold uppercase text-slate-500">
                <Icon size={13} />
                <h3 className="text-xs font-semibold uppercase text-slate-500">{title}</h3>
            </div>
            {children}
        </section>
    );
}

function ProfessionalTaskCard({ task }) {
    return (
        <article className="surface-row block px-3 py-3">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <h3 className="text-sm font-semibold leading-5 text-slate-950">{task.title}</h3>
                    <p className="mt-0.5 text-xs text-slate-500">{task.project}</p>
                </div>
                {task.url && (
                    <a href={task.url} className="text-slate-400 hover:text-slate-700" title={task.action_label || 'Open task'}>
                        <ArrowUpRight className="shrink-0" size={15} />
                    </a>
                )}
            </div>

            <div className="mt-3 flex flex-wrap items-center gap-2">
                <UrgencyBadge value={task.urgency} />
                <StatusBadge value={task.priority} priority />
                <StatusBadge value={task.status} />
                <span className={cx(
                    'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset',
                    task.is_overdue ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-slate-100 text-slate-600 ring-slate-200',
                )}>
                    {task.is_overdue ? 'Overdue' : (task.due_date ? `Due ${task.due_date}` : 'No due date')}
                </span>
            </div>

            <dl className="mt-3 grid gap-2 sm:grid-cols-2">
                <div>
                    <dt className="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Reason</dt>
                    <dd className="mt-1 text-xs leading-5 text-slate-600">{task.why}</dd>
                </div>
                <div>
                    <dt className="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Recommended Action</dt>
                    <dd className="mt-1 text-xs font-medium leading-5 text-slate-700">{task.recommended_action}</dd>
                </div>
            </dl>
        </article>
    );
}

function TeamReportAgent({ reportUrl, user, className = '' }) {
    const [report, setReport] = React.useState(null);
    const [loading, setLoading] = React.useState(false);
    const [error, setError] = React.useState('');
    const [copied, setCopied] = React.useState(false);

    const loadReport = async () => {
        if (!reportUrl || loading) return;

        setLoading(true);
        setError('');

        try {
            const response = await fetch(reportUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Report request failed');
            }

            const payload = await response.json();
            setReport(payload.report);
        } catch (reportError) {
            setError('Report could not be created. Try again.');
        } finally {
            setLoading(false);
        }
    };

    const printReport = () => {
        if (!report) return;

        window.requestAnimationFrame(() => window.print());
    };

    const reportTasks = report?.tasks ?? [];
    const reportMetrics = {
        recommended: reportTasks.length,
        highUrgency: reportTasks.filter((task) => task.urgency === 'high').length,
        overdue: reportTasks.filter((task) => task.is_overdue).length,
        blocked: reportTasks.filter((task) => task.is_blocked).length,
    };
    const nextActions = reportTasks
        .map((task) => task.recommended_action)
        .filter(Boolean)
        .slice(0, 5);
    const copyReport = async () => {
        if (!report) return;

        try {
            await window.navigator.clipboard.writeText(buildReportText(report, user, reportMetrics, nextActions));
            setCopied(true);
            window.setTimeout(() => setCopied(false), 1800);
        } catch (copyError) {
            setError('Report was generated, but copying is not available in this browser.');
        }
    };

    return (
        <section className={cx('card overflow-hidden p-0', className)} data-print-report={report ? 'true' : undefined}>
            <div className="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <div>
                    <div className="flex items-center gap-2">
                        <Sparkles size={16} className="text-cyan-700" />
                        <h2 className="text-sm font-semibold text-slate-950">Professional work report</h2>
                    </div>
                    {report && (
                        <p className="mt-1 text-xs text-slate-500">
                            {reportSourceLabel(report)}
                        </p>
                    )}
                </div>
                <div className="flex shrink-0 items-center gap-2" data-print-report-controls>
                    {report && (
                        <>
                            <button
                                type="button"
                                onClick={copyReport}
                                className="btn-secondary !min-h-9 !px-3"
                                title="Copy report"
                            >
                                <Copy size={14} />
                                <span>{copied ? 'Copied' : 'Copy'}</span>
                            </button>
                            <button
                                type="button"
                                onClick={printReport}
                                className="btn-secondary !min-h-9 !px-3"
                                title="Print report"
                            >
                                <Printer size={14} />
                                <span>Print</span>
                            </button>
                        </>
                    )}
                    <button
                        type="button"
                        onClick={loadReport}
                        disabled={loading}
                        className="btn-secondary !min-h-9 !px-3"
                        title={report ? 'Refresh report' : 'Generate report'}
                    >
                        <Sparkles size={14} className={loading ? 'animate-pulse' : ''} />
                        <span>{loading ? 'Working' : (report ? 'Refresh' : 'Generate')}</span>
                    </button>
                </div>
            </div>

            <div className="space-y-3 p-3">
                {!report && !error && (
                    <div className="surface-row px-3 py-4 text-sm text-slate-500">
                        No report generated yet.
                    </div>
                )}

                {error && (
                    <div className="surface-row border-rose-200 bg-rose-50 px-3 py-3 text-sm text-rose-700">
                        {error}
                    </div>
                )}

                {report && (
                    <>
                        {report.notice && (
                            <div className="surface-row border-amber-200 bg-amber-50 px-3 py-3 text-xs text-amber-800">
                                {report.notice}
                            </div>
                        )}

                        <div className="surface-row px-3 py-4">
                            <p className="text-[11px] font-semibold uppercase tracking-wide text-cyan-700">Work Report</p>
                            <h3 className="mt-1 text-lg font-semibold leading-6 text-slate-950">{user?.name || 'Team member'}</h3>
                            <p className="mt-2 text-sm leading-6 text-slate-600">{report.summary}</p>
                        </div>

                        <div className="grid grid-cols-2 gap-2">
                            <ReportMetric label="Recommended" value={reportMetrics.recommended} />
                            <ReportMetric label="High Urgency" value={reportMetrics.highUrgency} />
                            <ReportMetric label="Overdue" value={reportMetrics.overdue} />
                            <ReportMetric label="Blocked" value={reportMetrics.blocked} />
                        </div>

                        <ReportSection icon={FileText} title="Report Details">
                            <div className="grid gap-2">
                                <ReportMetaItem label="Prepared For" value={user?.name} />
                                <ReportMetaItem label="Role" value={formatRole(user?.role)} />
                                <ReportMetaItem label="Generated On" value={formatReportDate(report.generated_at)} />
                                <ReportMetaItem label="Source" value={reportSourceLabel(report)} />
                            </div>
                        </ReportSection>

                        <ReportSection icon={Sparkles} title="Executive Summary">
                            <p className="text-sm font-semibold leading-6 text-slate-950">{report.summary}</p>
                            <p className="mt-2 text-sm leading-6 text-slate-600">{report.focus}</p>
                        </ReportSection>

                        <ReportSection icon={ClipboardList} title="Priority Work">
                            {reportTasks.length > 0 ? (
                                <div className="space-y-2">
                                    {reportTasks.map((task) => (
                                        <ProfessionalTaskCard key={task.id} task={task} />
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-slate-500">No open assigned tasks.</p>
                            )}
                        </ReportSection>

                        <ReportSection icon={AlertTriangle} title="Risk And Watch List">
                            {report.blockers?.length > 0 ? (
                                <ul className="space-y-2 text-sm leading-6 text-slate-600">
                                    {report.blockers.map((blocker) => (
                                        <li key={blocker} className="rounded-md border border-slate-200 bg-slate-50 px-3 py-2">
                                            {blocker}
                                        </li>
                                    ))}
                                </ul>
                            ) : (
                                <p className="text-sm text-slate-500">No immediate blockers detected.</p>
                            )}
                        </ReportSection>

                        <ReportSection icon={Target} title="Recommended Next Steps">
                            {nextActions.length > 0 ? (
                                <ol className="space-y-2 text-sm leading-6 text-slate-600">
                                    {nextActions.map((action, index) => (
                                        <li key={`${action}-${index}`} className="flex gap-2">
                                            <span className="font-semibold text-slate-950">{index + 1}.</span>
                                            <span>{action}</span>
                                        </li>
                                    ))}
                                </ol>
                            ) : (
                                <p className="text-sm text-slate-500">No next action required.</p>
                            )}
                        </ReportSection>
                    </>
                )}
            </div>
        </section>
    );
}

function ReportLaunchCard({ reportUrl }) {
    return (
        <section className="card p-4">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <div className="flex items-center gap-2 text-sm font-semibold text-slate-950">
                        <FileText size={17} className="text-cyan-700" />
                        AI work report
                    </div>
                    <p className="mt-2 text-sm leading-6 text-slate-500">Dedicated printable report workspace.</p>
                </div>
                <Sparkles size={18} className="shrink-0 text-slate-400" />
            </div>
            <a href={reportUrl} className="btn-primary mt-4 w-full justify-center">
                <ArrowUpRight size={14} />
                Open report
            </a>
        </section>
    );
}

function ThemeToggle() {
    const getInitialTheme = () => {
        const stored = window.localStorage.getItem('pm-theme');
        if (stored === 'dark' || stored === 'light') {
            return stored;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    const [theme, setTheme] = React.useState(getInitialTheme);

    React.useEffect(() => {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        window.localStorage.setItem('pm-theme', theme);
    }, [theme]);

    return (
        <button
            type="button"
            onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}
            className="btn-secondary !px-3"
            title="Toggle theme"
        >
            {theme === 'dark' ? <Sun size={16} /> : <Moon size={16} />}
        </button>
    );
}

function ProjectDashboard({ data }) {
    const completion = data.stats.total_projects > 0
        ? Math.round((data.stats.completed_projects / data.stats.total_projects) * 100)
        : 0;

    return (
        <div className="py-6">
            <section className="page-hero-panel mb-5 overflow-hidden">
                <div className="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-slate-950">Welcome back, {data.user.name}</h1>
                        <p className="mt-1 text-sm text-slate-500">
                            Operational view for priorities, delivery health, activity, and team visibility.
                        </p>
                    </div>
                    <div className="flex flex-wrap items-center gap-3">
                        <div className="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                            <LayoutDashboard size={14} />
                            Project command center
                        </div>
                        <div className="flex h-9 items-center gap-2 rounded-md border border-slate-200 bg-slate-50 px-3 text-sm text-slate-600">
                            <ShieldCheck size={14} />
                            <span className="capitalize">{formatRole(data.user.role)}</span>
                        </div>
                        <a href={data.routes.projectsIndex} className="btn-secondary">
                            <FolderKanban size={14} />
                            Projects
                        </a>
                        {data.user.canCreateProject && data.routes.projectsCreate && (
                            <a href={data.routes.projectsCreate} className="btn-primary">
                                <Plus size={14} />
                                Create Project
                            </a>
                        )}
                        {data.user.canCreateTask && data.routes.tasksCreate && (
                            <a href={data.routes.tasksCreate} className="btn-secondary">
                                <ListChecks size={14} />
                                New Task
                            </a>
                        )}
                    </div>
                </div>
                <div className="grid gap-4 p-4 sm:grid-cols-2 xl:grid-cols-5">
                    <MetricCard icon={FolderKanban} label="Projects" value={data.stats.total_projects} helper={`${completion}% completed`} />
                    <MetricCard icon={BarChart3} label="Active" value={data.stats.active_projects} helper="Currently in delivery" tone="text-blue-700" />
                    <MetricCard icon={CheckCircle2} label="Completed" value={data.stats.completed_projects} helper="Closed successfully" tone="text-emerald-700" />
                    <MetricCard icon={Clock3} label="Pending" value={data.stats.pending_projects} helper="Waiting to start" tone="text-amber-700" />
                    <MetricCard icon={ListChecks} label="Open Tasks" value={data.stats.pending_tasks} helper={`${data.stats.done_tasks} tasks done`} tone="text-rose-700" />
                </div>
            </section>

            <div className="grid gap-5 xl:grid-cols-[1fr_360px]">
                <section className="card overflow-hidden p-0">
                    <div className="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <div>
                            <h2 className="text-sm font-semibold text-slate-950">Project portfolio</h2>
                            <p className="text-xs text-slate-500">Prioritized projects with delivery progress</p>
                        </div>
                        <a href={data.routes.projectsIndex} className="inline-flex items-center gap-1 text-sm font-medium text-slate-700 hover:text-slate-950">
                            View all <ArrowUpRight size={15} />
                        </a>
                    </div>
                    <div>
                        {data.projects.length > 0
                            ? data.projects.map((project) => <ProjectRow key={project.id} project={project} />)
                            : <p className="px-4 py-10 text-center text-sm text-slate-500">No projects yet.</p>}
                    </div>
                </section>

                <aside className="space-y-5">
                    {data.routes.teamReportPage && (
                        <ReportLaunchCard reportUrl={data.routes.teamReportPage} />
                    )}

                    <section className="card overflow-hidden p-0">
                        <div className="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                            <div>
                                <h2 className="text-sm font-semibold text-slate-950">Focus tasks</h2>
                                <p className="text-xs text-slate-500">Recent work requiring attention</p>
                            </div>
                            <CalendarClock className="text-slate-400" size={18} />
                        </div>
                        <div className="p-1">
                            {data.recentTasks.length > 0
                                ? data.recentTasks.map((task) => <TaskItem key={task.id} task={task} />)
                                : <p className="px-3 py-8 text-center text-sm text-slate-500">No tasks yet.</p>}
                        </div>
                    </section>

                    {data.user.canViewActivityLogs ? (
                        <section className="card overflow-hidden p-0">
                            <div className="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                                <div>
                                    <h2 className="text-sm font-semibold text-slate-950">Activity stream</h2>
                                    <p className="text-xs text-slate-500">Changes recorded today</p>
                                </div>
                                <a href={data.routes.activityLogs} className="text-slate-400 hover:text-slate-700" title="Open audit log">
                                    <ArrowUpRight size={17} />
                                </a>
                            </div>
                            <div className="divide-y divide-slate-100">
                                {data.recentLogs.length > 0
                                    ? data.recentLogs.map((log) => <ActivityItem key={log.id} log={log} />)
                                    : <p className="px-3 py-8 text-center text-sm text-slate-500">No activity yet.</p>}
                            </div>
                        </section>
                    ) : (
                        <section className="card p-4">
                            <div className="flex items-center gap-2 text-sm font-semibold text-slate-950">
                                <Eye size={17} className="text-slate-500" />
                                Team workspace
                            </div>
                            <p className="mt-3 text-sm leading-6 text-slate-500">
                                You can review assigned projects, update your own tasks, follow delivery status, and open project details. Administrative audit activity is reserved for managers.
                            </p>
                        </section>
                    )}
                </aside>
            </div>

            <section className="mt-5 grid gap-5 lg:grid-cols-3">
                <div className="card p-4">
                    <div className="flex items-center gap-2 text-sm font-semibold text-slate-950">
                        <Sparkles size={17} className="text-blue-600" />
                        Delivery health
                    </div>
                    <p className="metric-number mt-3 text-3xl font-semibold text-slate-950">{completion}%</p>
                    <p className="mt-1 text-sm text-slate-500">Project completion rate across accessible work.</p>
                </div>
                <div className="card p-4">
                    <div className="flex items-center gap-2 text-sm font-semibold text-slate-950">
                        <Users size={17} className="text-emerald-600" />
                        Team visibility
                    </div>
                    <p className="metric-number mt-3 text-3xl font-semibold text-slate-950">
                        {data.projects.reduce((total, project) => total + project.members.length, 0)}
                    </p>
                    <p className="mt-1 text-sm text-slate-500">Member assignments visible in current portfolio.</p>
                </div>
                <div className="card p-4">
                    <div className="flex items-center gap-2 text-sm font-semibold text-slate-950">
                        <Search size={17} className="text-violet-600" />
                        Task pipeline
                    </div>
                    <p className="metric-number mt-3 text-3xl font-semibold text-slate-950">
                        {data.stats.todo_tasks}/{data.stats.in_progress_tasks}/{data.stats.done_tasks}
                    </p>
                    <p className="mt-1 text-sm text-slate-500">To do, in progress, and completed task split.</p>
                </div>
            </section>
        </div>
    );
}

function AiReportPage({ data }) {
    return (
        <div className="py-6">
            <section className="page-hero-panel mb-5 overflow-hidden">
                <div className="flex flex-col gap-4 border-b border-slate-200 px-5 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-slate-950">AI work report</h1>
                        <p className="mt-1 text-sm text-slate-500">Professional task report for {data.user.name}.</p>
                    </div>
                    <a href={data.routes.dashboard} className="btn-secondary">
                        <LayoutDashboard size={14} />
                        Dashboard
                    </a>
                </div>
            </section>

            <div className="mx-auto max-w-4xl">
                <TeamReportAgent
                    reportUrl={data.routes.teamReport}
                    user={data.user}
                    className="ai-report-document"
                />
            </div>
        </div>
    );
}

const mount = document.getElementById('project-dashboard');

if (mount) {
    createRoot(mount).render(
        <ProjectDashboard data={JSON.parse(mount.dataset.dashboard)} />,
    );
}

const reportMount = document.getElementById('ai-report-page');

if (reportMount) {
    createRoot(reportMount).render(
        <AiReportPage data={JSON.parse(reportMount.dataset.report)} />,
    );
}

const themeToggleMount = document.getElementById('theme-toggle-root');

if (themeToggleMount) {
    createRoot(themeToggleMount).render(<ThemeToggle />);
}

const landingMount = document.getElementById('projectmanager-landing');

if (landingMount) {
    createRoot(landingMount).render(<ProjectManagerLanding />);
}

const contactMount = document.getElementById('projectmanager-contact');

if (contactMount) {
    createRoot(contactMount).render(<ContactPage />);
}

document.querySelectorAll('.js-flash').forEach((flash) => {
    window.setTimeout(() => {
        flash.classList.add('transition-opacity', 'duration-300', 'opacity-0');
        window.setTimeout(() => flash.remove(), 300);
    }, 3500);
});

const projectsPage = document.getElementById('projects-index-page');
if (projectsPage) {
    const input = projectsPage.querySelector('[data-project-filter]');
    const cards = Array.from(projectsPage.querySelectorAll('[data-project-item]'));
    const emptyState = projectsPage.querySelector('[data-projects-empty-state]');

    if (input && cards.length > 0) {
        const filter = () => {
            const value = input.value.trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach((card) => {
                const haystack = card.getAttribute('data-project-text') ?? '';
                const visible = value === '' || haystack.includes(value);
                card.classList.toggle('hidden', !visible);
                if (visible) visibleCount += 1;
            });

            if (emptyState) {
                emptyState.classList.toggle('hidden', visibleCount > 0);
            }
        };

        input.addEventListener('input', filter);
    }
}

const commandPalette = document.querySelector('[data-command-palette]');
if (commandPalette) {
    const openButtons = document.querySelectorAll('[data-command-open]');
    const closeButtons = commandPalette.querySelectorAll('[data-command-close]');
    const input = commandPalette.querySelector('[data-command-input]');
    const items = Array.from(commandPalette.querySelectorAll('[data-command-item]'));
    const emptyState = commandPalette.querySelector('[data-command-empty]');

    const filterItems = () => {
        const query = (input?.value || '').trim().toLowerCase();
        let visibleCount = 0;

        items.forEach((item) => {
            const haystack = item.getAttribute('data-command-text') || '';
            const isVisible = query === '' || haystack.includes(query);
            item.classList.toggle('hidden', !isVisible);
            if (isVisible) visibleCount += 1;
        });

        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount !== 0);
        }
    };

    const openPalette = () => {
        commandPalette.classList.remove('hidden');
        commandPalette.setAttribute('aria-hidden', 'false');
        filterItems();
        window.setTimeout(() => input?.focus(), 0);
    };

    const closePalette = () => {
        commandPalette.classList.add('hidden');
        commandPalette.setAttribute('aria-hidden', 'true');
        if (input) input.value = '';
    };

    openButtons.forEach((button) => button.addEventListener('click', openPalette));
    closeButtons.forEach((button) => button.addEventListener('click', closePalette));
    input?.addEventListener('input', filterItems);

    document.addEventListener('keydown', (event) => {
        const isK = event.key.toLowerCase() === 'k';
        const hasMeta = event.ctrlKey || event.metaKey;

        if (hasMeta && isK) {
            event.preventDefault();
            if (commandPalette.classList.contains('hidden')) {
                openPalette();
            } else {
                closePalette();
            }
            return;
        }

        if (event.key === 'Escape' && !commandPalette.classList.contains('hidden')) {
            closePalette();
        }
    });
}
