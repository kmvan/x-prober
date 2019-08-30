const getElementOffsetTop = (e: HTMLElement): number => {
  return e.getBoundingClientRect().top + window.pageYOffset
}

export default getElementOffsetTop
