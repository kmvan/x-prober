import { observer } from 'mobx-react-lite'
import React, { FC } from 'react'
import { CardGrid } from '../../../Card/src/components/card-grid'
import { Row } from '../../../Grid/src/components/row'
import { Alert } from '../../../Utils/src/components/alert'
import { DatabaseStore } from '../stores'

export const Database: FC = observer(() => {
  const { conf } = DatabaseStore
  const shortItems: [string, boolean | string][] = [
    ['SQLite3', conf?.sqlite3],
    ['SQLite', conf?.sqliteLibversion],
    ['MySQLi client', conf?.mysqliClientVersion],
    ['Mongo', conf?.mongo],
    ['MongoDB', conf?.mongoDb],
    ['PostgreSQL', conf?.postgreSql],
    ['Paradox', conf?.paradox],
    ['MS SQL', conf?.msSql],
    ['File Pro', conf?.filePro],
    ['MaxDB client', conf?.maxDbClient],
    ['MaxDB server', conf?.maxDbServer],
  ]
  return (
    <Row>
      {shortItems.map(([name, content]) => (
        <CardGrid
          key={name}
          name={name}
          mobileMd={[1, 2]}
          tablet={[1, 3]}
          desktopMd={[1, 4]}
          desktopLg={[1, 5]}
        >
          <Alert isSuccess={Boolean(content)} msg={content} />
        </CardGrid>
      ))}
    </Row>
  )
})
