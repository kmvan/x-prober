/**
 * @version 1.0.5
 */

const glob = require('glob')
const fs = require('fs')
const PO = require('pofile')
const path = require('path')
const dirSrc = path.resolve(__dirname, 'src')
const dirComponents = `${dirSrc}/Components`
const langs = {}
const poEntries = {}
const JSON2 = require('JSON2')
const deepSort = require('deep-sort-object')
const parseFile = (filePath) => {
  const code = fs.readFileSync(filePath).toString()
  const reg = new RegExp(
    `gettext\\s*\\(\\s*('.+?')\\s*,*\\s*('.+?')*\\s*\\)`,
    'gm'
  )

  const matches = code.matchAll(reg)

  if (matches) {
    for (const match of matches) {
      const msgid = match[1].slice(1, -1)
      const msgctxt = (match[2] || '').slice(1, -1)
      if (poEntries[`${msgid}${msgctxt}`]) {
        continue
      }

      poEntries[`${msgid}${msgctxt}`] = `
${msgctxt ? `msgctxt ${JSON2.stringify(msgctxt)}` : ''}
msgid ${JSON2.stringify(msgid)}
msgstr ""
`.trim()
    }
  }
}

const fetchDirOrFile = (filePathOrDir) => {
  if (fs.lstatSync(filePathOrDir).isDirectory()) {
    fs.readdirSync(filePathOrDir).map((p) =>
      fetchDirOrFile(`${filePathOrDir}/${p}`)
    )
  } else {
    if (['.ts', '.tsx'].includes(path.extname(filePathOrDir))) {
      parseFile(filePathOrDir)
    }
  }
}

const createPot = () => {
  const toWriteData = `
#, fuzzy
msgid ""
msgstr ""
"Project-Id-Version: \\n"
"POT-Creation-Date: \\n"
"PO-Revision-Date: \\n"
"Last-Translator: \\n"
"Language-Team: \\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
"X-Generator: Poedit 2.2.1\\n"
"X-Poedit-Basepath: ../src\\n"
"Plural-Forms: nplurals=2; plural=(n != 1);\\n"
"X-Poedit-SourceCharset: UTF-8\\n"
"X-Poedit-KeywordsList: gettext\\n"

${Object.values(poEntries).join('\n\n')}
`.trim()
  // write pot
  fs.writeFileSync(
    path.resolve(__dirname, 'languages/lang.pot'),
    toWriteData,
    'utf8'
  )
}

fetchDirOrFile(dirComponents)
// create pot
createPot()

const formatItem = (items) => {
  return items.map(({ msgctxt, msgid, msgstr }) => {
    return {
      msgctxt,
      msgid,
      msgstr: msgstr[0],
    }
  })
}

const getItem = async (filepath) => {
  return new Promise((resolve) => {
    PO.load(filepath, (err, data) => {
      const langId = path.basename(filepath, '.po')
      const items = formatItem(data.items)
      resolve({
        items,
        langId,
      })
    })
  })
}

const writeJsData = ({ langId, items }) => {
  items.map(({ msgstr, msgid, msgctxt }) => {
    const key = `${msgctxt || ''}${msgid}`
    if (!langs[key]) {
      langs[key] = {}
    }
    langId = langId.toLowerCase().replace('-', '').replace('_', '')
    langs[key][langId] = msgstr
  })

  fs.writeFileSync(
    path.resolve(__dirname, 'src/Components/Language/src/lang.json'),
    JSON2.stringify(deepSort(langs), null, 2),
    (err) => {
      if (err) {
        throw err
      }
    }
  )
}

glob.sync(path.resolve(__dirname, 'languages/*.po')).map(async (filepath) => {
  writeJsData(await getItem(filepath))
})
