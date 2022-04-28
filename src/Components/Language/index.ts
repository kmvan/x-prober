import langs from './data.json'
const langId = navigator.language
  .replace('-', '')
  .replace('_', '')
  .toLowerCase()
export const gettext = (text: string, context = ''): string => {
  const id = `${context || ''}${text}`
  return langs?.[id]?.[langId] ?? text
}
