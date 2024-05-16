export default [
  {
    title: 'Home',
    to: { name: 'root' },
    icon: { icon: 'tabler-align-box-bottom-center' },
  },
  {
    title: 'Members',
    icon: { icon: 'tabler-users' },
    to: { name: 'members-list' },
  },
  {
    title: 'Projects',
    icon: { icon: 'tabler-chart-histogram' },
    children: [
      {
        title: 'Website Design Project',
        to: { name: 'web-designs-list' },
      },
      {
        title: 'SEO Program',
        to: { name: 'seo-programs-list' },
      },
      {
        title: 'Google Ads Program',
        to: { name: 'google-ads-programs' },
      },
    ],
  },
  {
    title: 'Logout',
    icon: { icon: 'tabler-logout' },
  },
]
