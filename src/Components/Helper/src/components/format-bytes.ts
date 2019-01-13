const formatBytes = (bytes: number, decimals: number = 2): string => {
  if (bytes === 0) {
    return '0'
  }

  const k = 1024
  const sizes = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y']
  const i = Math.floor(Math.log(bytes) / Math.log(k))

  return parseFloat((bytes / Math.pow(k, i)).toFixed(decimals)) + ' ' + sizes[i]
}

export default formatBytes
