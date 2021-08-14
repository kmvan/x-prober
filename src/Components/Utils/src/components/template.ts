export interface TemplatePlaceholdersProps {
  [id: string]: string | number
}
export function template(
  str: string,
  placeholders: TemplatePlaceholdersProps
): string {
  for (const [k, v] of Object.entries(placeholders)) {
    const reg = new RegExp(`\\{\\{${k}\\}\\}`, 'g')
    str = str.replace(reg, String(v))
  }
  return str
}
