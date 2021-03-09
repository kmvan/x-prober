export const getElementOffsetTop = (e: HTMLElement): number => {
  return Math.round(e.getBoundingClientRect().top + window.pageYOffset)
}
