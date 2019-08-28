export const size = {
  mobileSm: '320px',
  mobileMd: '375px',
  mobileLg: '425px',
  tablet: '768px',
  desktopSm: '1024px',
  desktopMd: '1440px',
  desktopLg: '2560px',
}

export const device = (id: string): string => {
  if (!size[id]) {
    return ''
  }

  return `(min-width: ${size[id]})`
}
