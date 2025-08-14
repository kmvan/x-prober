export interface TemplatePlaceholdersProps {
  [id: string]: string | number;
}
export function template(
  str: string,
  placeholders: TemplatePlaceholdersProps
): string {
  let text = str;
  for (const [k, v] of Object.entries(placeholders)) {
    const reg = new RegExp(`\\{\\{${k}\\}\\}`, 'g');
    text = text.replace(reg, String(v));
  }
  return text;
}
