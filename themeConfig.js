import { breakpointsVuetify } from '@vueuse/core'
import { VIcon } from 'vuetify/components/VIcon'
import { defineThemeConfig } from '@core'
import { Skins } from '@core/enums'

// ❗ Logo SVG must be imported with ?raw suffix
import logo from '@images/logo.png'
import logoHalf from '@images/half-logo.png'
import { AppContentLayoutNav, ContentWidth, FooterType, NavbarType } from '@layouts/enums'

const store = JSON.parse(localStorage.getItem('auth'))

const getDynamicLogo = () => (store && store.tenant && store.logo  ? store.logo : logo)
const getDynamicFavicon = () => (store && store.tenant && store.favicon  ? store.favicon : logoHalf)
const getDynamicTitle = () => (store && store.tenant ? store.title : 'Darby Dash')


const selectedTheme = localStorage.getItem('selectedTheme') || 'system'

export const { themeConfig, layoutConfig } = defineThemeConfig({
  app: {
    title: getDynamicTitle(),
    logo: h('div', {
      innerHTML: `<img src="${getDynamicLogo()}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:40px;">`,
    }),
    logoHalf: h('div', {
      innerHTML: `<img src="${getDynamicFavicon()}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:40px;">`,
    }),
    watermarkLogo: h('div', {
      innerHTML: `<img src="${getDynamicLogo()}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:30px;">`,
    }),
    contentWidth: ContentWidth.Boxed,
    contentLayoutNav: AppContentLayoutNav.Vertical,
    overlayNavFromBreakpoint: breakpointsVuetify.md + 16, // 16 for scrollbar. Docs: https://next.vuetifyjs.com/en/features/display-and-platform/
    i18n: {
      enable: false,
      defaultLocale: 'en',
      langConfig: [
        {
          label: 'English',
          i18nLang: 'en',
          isRTL: false,
        },
        {
          label: 'French',
          i18nLang: 'fr',
          isRTL: false,
        },
        {
          label: 'Arabic',
          i18nLang: 'ar',
          isRTL: true,
        },
      ],
    },
    theme: selectedTheme,
    skin: Skins.Default,
    iconRenderer: VIcon,
  },
  navbar: {
    type: NavbarType.Sticky,
    navbarBlur: true,
  },
  footer: { type: FooterType.Static },
  verticalNav: {
    isVerticalNavCollapsed: false,
    defaultNavItemIconProps: { icon: 'tabler-circle' },
    isVerticalNavSemiDark: false,
  },
  horizontalNav: {
    type: 'sticky',
    transition: 'slide-y-reverse-transition',
    popoverOffset: 6,
  },

  /*
    // ℹ️  In below Icons section, you can specify icon for each component. Also you can use other props of v-icon component like `color` and `size` for each icon.
    // Such as: chevronDown: { icon: 'tabler-chevron-down', color:'primary', size: '24' },
    */
  icons: {
    chevronDown: { icon: 'tabler-chevron-down' },
    chevronRight: { icon: 'tabler-chevron-right', size: 20 },
    close: { icon: 'tabler-x' },
    verticalNavPinned: { icon: 'tabler-circle-dot' },
    verticalNavUnPinned: { icon: 'tabler-circle' },
    sectionTitlePlaceholder: { icon: 'tabler-minus' },
  },
})
