export const getElementOffsetTop = (e: HTMLElement): number =>
  Math.round(e.getBoundingClientRect().top + window.pageYOffset)
