/**
 * @version 1.0.5
 */

const glob = require('glob')
const fs = require('fs')
const PO = require('pofile')
const path = require('path')
const langs = {
  js: {},
}

const formatItem = items => {
  return items.map(({ msgctxt, msgid, msgstr }) => {
    return {
      msgctxt,
      msgid,
      msgstr: msgstr[0],
    }
  })
}

const getItem = async filepath => {
  return new Promise(resolve => {
    PO.load(filepath, (err, data) => {
      const langId = data.headers.Language
      const items = formatItem(data.items)
      const filename = path.basename(filepath, '.po')
      const isPhp = filename.includes('php-')
      resolve({
        isPhp,
        items,
        langId,
      })
    })
  })
}

const writeJsData = data => {
  const newData = {}
  Object.entries(data).map(([langId, items]) => {
    items.map(({ msgstr, msgid, msgctxt }) => {
      const key = `${msgctxt || ''}${msgid}`
      if (!newData[key]) {
        newData[key] = {}
      }

      newData[key][langId] = msgstr
    })
  })
  fs.writeFileSync(
    path.resolve(__dirname, 'src/Components/Language/src/lang.json'),
    JSON.stringify(newData, null, 2),
    err => {
      if (err) {
        throw err
      }
    }
  )
}

glob.sync(path.resolve(__dirname, 'languages/*.po')).map(async filepath => {
  const { isPhp, items, langId } = await getItem(filepath)
  langs[isPhp ? 'php' : 'js'][langId] = items
  writeJsData(langs.js)
})
