export function template(str: string, placeholders: object): string {
  for (const [k, v] of Object.entries(placeholders)) {
    const reg = new RegExp(`\\{\\{${k}\\}\\}`, 'g')
    str = str.replace(reg, String(v))
  }
  return str
}
