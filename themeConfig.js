import { breakpointsVuetify } from '@vueuse/core'
import { VIcon } from 'vuetify/components/VIcon'
import { defineThemeConfig } from '@core'
import { Skins } from '@core/enums'
import { ref } from 'vue'
import { useAuthStore } from './resources/js/store/auth'

// ❗ Logo SVG must be imported with ?raw suffix
import logo from '@images/logo.png'
import logoHalf from '@images/half-logo.png'
import { AppContentLayoutNav, ContentWidth, FooterType, NavbarType } from '@layouts/enums'

const store = JSON.parse(localStorage.getItem('auth'))
const authStore = useAuthStore()

console.log('authStore', authStore)

const getDynamicLogo = () => {
  const role = authStore?.authenticated ? authStore?.authUser?.user?.roles[0]?.name : null
  
  console.log('role', role)
  if (role === 'Client User') {
    const companyLogo = store?.authUser?.user?.info?.company_logo
    
    console.log('companyLogo', companyLogo)
    
    return companyLogo
      ? `${window.location.origin}/images/company_logos/${companyLogo}`
      : logo
  } else {
    return store && store.logo ? store.logo : logo
  }
}

const getDynamicFavicon = () => (store && store.favicon ? store.favicon : logoHalf)
const getDynamicTitle = () => (store && store.tenant ? store.companyDisplayName : 'Darby Dash')

const systemLogo = ref(getDynamicLogo())
const systemLogoHalf = ref(getDynamicFavicon())

export const updateSystemLogo = async newLogo => {
  systemLogo.value = newLogo || logo
  updateLogoInDOM()
}

export const updateSystemFavicon = async newFavicon => {
  systemLogoHalf.value = newFavicon || logoHalf
  updateFaviconInDOM()
}

const updateLogoInDOM = () => {
  const logoElement = document.querySelector('.system-company-logo')
  const watermarkLogoElement = document.querySelector('.system-watermark-company-logo')
  if (logoElement) {
    logoElement.innerHTML = `<img src="${systemLogo.value}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:45px;">`
  }
  if (watermarkLogoElement) {
    watermarkLogoElement.innerHTML = `<img src="${systemLogo.value}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:30px;">`
  }
}

const updateFaviconInDOM = () => {
  const faviconElement = document.querySelector('.system-company-favicon')
  if (faviconElement) {
    faviconElement.innerHTML = `<img src="${systemLogoHalf.value}" alt="Favicon" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:40px;">`
  }
}

const selectedTheme = localStorage.getItem('selectedTheme') || 'system'

export const { themeConfig, layoutConfig } = defineThemeConfig({
  app: {
    title: getDynamicTitle(),
    logo: h('div', {
      class: 'system-company-logo',
      innerHTML: `<img src="${systemLogo.value}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:45px;">`,
    }),
    logoHalf: h('div', {
      class: 'system-company-favicon',
      innerHTML: `<img src="${systemLogoHalf.value}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:40px;">`,
    }),
    watermarkLogo: h('div', {
      class: 'system-watermark-company-logo',
      innerHTML: `<img src="${logo}" alt="Logo" style="line-height:0; color: rgb(var(--v-global-theme-primary));height:30px;">`,
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
    isVerticalNavCollapsed: true,
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
