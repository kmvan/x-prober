export const formatBytes = (bytes: number, decimals = 2): string => {
  if (bytes === 0) {
    return '0'
  }
  const k = 1024
  const sizes = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y']
  let i = Math.floor(Math.log(bytes) / Math.log(k))
  i = i < 0 ? 0 : i
  const num = parseFloat((bytes / k ** i).toFixed(decimals))
  if (!num) {
    return '0'
  }
  return `${num} ${sizes[i]}`
}
