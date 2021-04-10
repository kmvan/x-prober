export const versionCompare = (
  left: string,
  right: string
): boolean | number => {
  if (typeof left + typeof right !== 'stringstring') {
    return false
  }
  const a = left.split('.')
  const b = right.split('.')
  const len = Math.max(a.length, b.length)
  for (let i = 0; i < len; i++) {
    if ((a[i] && !b[i] && ~~a[i] > 0) || ~~a[i] > ~~b[i]) {
      return 1
    } else if ((b[i] && !a[i] && ~~b[i] > 0) || ~~a[i] < ~~b[i]) {
      return -1
    }
  }
  return 0
}
