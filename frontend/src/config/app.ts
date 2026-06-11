const env = import.meta.env

export const appConfig = {
  name:        env.VITE_APP_NAME        ?? 'OceanSafer',
  title:       env.VITE_APP_TITLE       ?? 'OceanSafer',
  description: env.VITE_APP_DESCRIPTION ?? 'Maritime safety management platform',

  logo: {
    header: env.VITE_LOGO_HEADER ?? '/images/logo/logo-pca.png',
    auth:   env.VITE_LOGO_AUTH   ?? '/images/logo/logo-white.png',
    home:   env.VITE_LOGO_HOME   ?? '/images/logo/logo.png',
  },

  developer: {
    name: env.VITE_DEV_NAME ?? '2UP',
    logo: env.VITE_DEV_LOGO ?? '/images/logo/logo-2up.png',
  },
}
