export const breakPoints = {
  mobileSm: '320px',
  mobileMd: '375px',
  mobileLg: '425px',
  tablet: '768px',
  desktopSm: '1024px',
  desktopMd: '1440px',
  desktopLg: '2560px',
}
export type DeviceIdProps =
  | 'mobileSm'
  | 'mobileMd'
  | 'mobileLg'
  | 'tablet'
  | 'desktopSm'
  | 'desktopMd'
  | 'desktopLg'
export const device = (id: DeviceIdProps): string => {
  if (!breakPoints[id]) {
    return ''
  }
  return `(min-width: ${breakPoints[id]})`
}
