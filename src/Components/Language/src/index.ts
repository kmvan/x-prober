interface ILangItem {
  [text: string]: {
    [langId: string]: string
  }
}

interface ILangs {
  [langId: string]: ILangItem[]
}

const langs: ILangs = require('./lang.json')
const langId = navigator.language.replace('-', '_')

export const gettext = (text: string, context: string = ''): string => {
  const id = `${context || ''}${text}`
  return (langs[id] && langs[id][langId]) || text
}
