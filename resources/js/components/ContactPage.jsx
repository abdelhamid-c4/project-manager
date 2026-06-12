import React from 'react';
import {
    ArrowLeft,
    CheckCircle2,
    ClipboardList,
    Clock,
    Mail,
    MessageSquare,
    Send,
    ShieldCheck,
} from 'lucide-react';

const loginPath = '/login';
const supportEmail = 'support@projectmanager.local';

function BrandMark({ size = 'h-10 w-10' }) {
    return (
        <div className={`brand-mark flex ${size} items-center justify-center rounded-lg text-white`}>
            <ClipboardList className="h-5 w-5" aria-hidden="true" />
        </div>
    );
}

const infoCards = [
    {
        icon: Mail,
        title: 'Support email',
        content: (
            <>
                <a href={`mailto:${supportEmail}`} className="auth-shell-link text-sm">
                    {supportEmail}
                </a>
                <p className="mt-2 text-sm text-slate-500">
                    For urgent matters, email directly with a clear subject line and your department.
                </p>
            </>
        ),
    },
    {
        icon: MessageSquare,
        title: 'What to include',
        content: (
            <ul className="space-y-3">
                {[
                    'Your full name and department',
                    'The role you need (Admin, PM, Team Member)',
                    'Project or workspace details',
                    'Any context or urgency',
                ].map((item) => (
                    <li key={item} className="flex gap-3 text-sm text-slate-600">
                        <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true" />
                        {item}
                    </li>
                ))}
            </ul>
        ),
    },
    {
        icon: Clock,
        title: 'Response time',
        content: (
            <p className="text-sm text-slate-600">
                Support requests are reviewed during business hours. Expect a follow-up within 1–2 business days for standard access requests.
            </p>
        ),
    },
    {
        icon: ShieldCheck,
        title: 'Internal use only',
        content: (
            <p className="text-sm text-slate-600">
                ProjectManager is not available for public self-service. All access is provisioned by the system administrator after identity verification.
            </p>
        ),
    },
];

function ContactForm() {
    const [sent, setSent] = React.useState(false);
    const [loading, setLoading] = React.useState(false);

    function handleSubmit(event) {
        event.preventDefault();
        const form = event.currentTarget;

        if (!form.reportValidity()) {
            return;
        }

        setLoading(true);
        window.setTimeout(() => {
            form.reset();
            setSent(true);
            setLoading(false);
        }, 600);
    }

    return (
        <form onSubmit={handleSubmit} className="card space-y-5" noValidate={false}>
            <div className="border-b border-slate-100 pb-5">
                <h2 className="text-xl font-bold text-slate-950">Send a message</h2>
                <p className="mt-1.5 text-sm text-slate-500">
                    Fill in the details below and the support team will follow up at the email you provide.
                </p>
            </div>

            <div className="grid gap-4 sm:grid-cols-2">
                <div>
                    <label htmlFor="contact-name" className="form-label">Full name</label>
                    <input
                        id="contact-name"
                        name="name"
                        type="text"
                        required
                        className="form-input"
                        placeholder="Your full name"
                    />
                </div>
                <div>
                    <label htmlFor="contact-email" className="form-label">Work email</label>
                    <input
                        id="contact-email"
                        name="email"
                        type="email"
                        required
                        className="form-input"
                        placeholder="you@company.com"
                    />
                </div>
            </div>

            <div>
                <label htmlFor="contact-department" className="form-label">
                    Department <span className="font-normal text-slate-400">(optional)</span>
                </label>
                <input
                    id="contact-department"
                    name="department"
                    type="text"
                    className="form-input"
                    placeholder="Your team or department"
                />
            </div>

            <div>
                <label htmlFor="contact-subject" className="form-label">Subject</label>
                <input
                    id="contact-subject"
                    name="subject"
                    type="text"
                    required
                    className="form-input"
                    placeholder="e.g. Access request, Role change, Account issue"
                />
            </div>

            <div>
                <label htmlFor="contact-message" className="form-label">Message</label>
                <textarea
                    id="contact-message"
                    name="message"
                    rows={6}
                    required
                    className="form-input"
                    placeholder="Describe what you need. Include any relevant project names, user accounts, or context that helps the support team act quickly."
                />
            </div>

            {sent ? (
                <div className="rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-4">
                    <div className="flex items-start gap-3">
                        <CheckCircle2 className="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" aria-hidden="true" />
                        <div>
                            <p className="text-sm font-bold text-emerald-800">Message received</p>
                            <p className="mt-0.5 text-sm text-emerald-700">
                                Your message has been submitted. The support team will review and follow up at the email you provided.
                            </p>
                        </div>
                    </div>
                </div>
            ) : (
                <button type="submit" disabled={loading} className="btn-primary w-full sm:w-auto">
                    {loading ? (
                        <span
                            className="inline-block h-4 w-4 animate-spin rounded-full border-2 border-white/30 border-t-white"
                            aria-hidden="true"
                        />
                    ) : (
                        <Send className="h-4 w-4" aria-hidden="true" />
                    )}
                    {loading ? 'Sending…' : 'Send message'}
                </button>
            )}
        </form>
    );
}

export default function ContactPage() {
    return (
        <div className="min-h-screen bg-transparent text-slate-900">
            {/* ── Header ── */}
            <header className="sticky top-0 z-50 border-b border-slate-200 bg-white/76 shadow-sm backdrop-blur-xl">
                <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                    <a href="/" className="flex items-center gap-2.5" aria-label="ProjectManager home">
                        <BrandMark size="h-9 w-9" />
                        <span className="font-display hidden text-lg font-bold text-slate-900 sm:block">ProjectManager</span>
                    </a>
                    <div className="flex items-center gap-3">
                        <a href="/" className="btn-secondary gap-1.5">
                            <ArrowLeft className="h-4 w-4" aria-hidden="true" />
                            Back
                        </a>
                        <a href={loginPath} className="btn-primary">
                            Log In
                        </a>
                    </div>
                </div>
            </header>

            <main>
                {/* ── Page hero ── */}
                <section className="px-4 pb-12 pt-12 sm:px-6 lg:px-8 lg:pb-14 lg:pt-16">
                    <div className="mx-auto max-w-2xl text-center">
                        <span className="badge bg-cyan-100 text-cyan-800">Support</span>
                        <h1 className="mt-5 text-4xl font-bold text-slate-950 sm:text-5xl">
                            Get in touch with the support team.
                        </h1>
                        <p className="mt-5 text-base leading-8 text-slate-600">
                            Contact us for workspace access, role changes, account issues, or project setup help.
                            All access is controlled and provisioned by the internal administrator.
                        </p>
                    </div>
                </section>

                {/* ── Two-column contact layout ── */}
                <section className="px-4 pb-20 sm:px-6 lg:px-8">
                    <div className="mx-auto grid max-w-7xl gap-6 lg:grid-cols-[1fr_1.55fr] lg:items-start">

                        {/* Info cards */}
                        <div className="space-y-4">
                            {infoCards.map(({ icon: Icon, title, content }) => (
                                <div key={title} className="card">
                                    <div className="mb-4 flex items-center gap-3">
                                        <div className="icon-action shrink-0" aria-hidden="true">
                                            <Icon className="h-5 w-5" />
                                        </div>
                                        <h2 className="text-base font-bold text-slate-950">{title}</h2>
                                    </div>
                                    {content}
                                </div>
                            ))}
                        </div>

                        {/* Form */}
                        <ContactForm />
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
                            <p className="mt-3 text-sm text-slate-500">
                                Internal project command center. Not intended for public self-service access.
                            </p>
                        </div>
                        <div className="flex flex-wrap gap-x-8 gap-y-3 text-sm text-slate-500">
                            <a href="/" className="hover:text-slate-900 transition-colors">Home</a>
                            <a href="/#features" className="hover:text-slate-900 transition-colors">Features</a>
                            <a href="/#security" className="hover:text-slate-900 transition-colors">Security</a>
                            <a href={`mailto:${supportEmail}`} className="hover:text-slate-900 transition-colors">{supportEmail}</a>
                        </div>
                        <p className="text-sm text-slate-400 lg:shrink-0">&copy; {new Date().getFullYear()} ProjectManager.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
}
