import langs from './lang.json'
const langId = navigator.language
  .replace('-', '')
  .replace('_', '')
  .toLowerCase()
export const gettext = (text: string, context: string = ''): string => {
  const id = `${context || ''}${text}`
  return langs?.[id]?.[langId] ?? text
}
