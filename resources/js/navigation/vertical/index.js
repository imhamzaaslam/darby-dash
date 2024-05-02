export default [
    {
        title: 'Home',
        to: { name: 'root' },
        icon: { icon: 'tabler-align-box-bottom-center' },
    },
    {
        title: 'Project Dashboards',
        icon: { icon: 'tabler-smart-home' },
        children: [
            { title: 'Website Design Project', to: 'web-development-dash' },
            { title: 'SEO Program', to: 'seo-programme' },
            { title: 'Google Ads', to: 'google-ads' },
        ],
    },
    {
        title: 'Projects',
        to: { name: 'projects' },
        icon: { icon: 'tabler-stack' },
    }
]
