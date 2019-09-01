import React, { Component } from 'react'
import { observer } from 'mobx-react'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store from '../stores'
import Alert from '~components/Helper/src/components/alert'

@observer
class Database extends Component {
  public render() {
    const { conf } = store
    const shortItems: any[] = [
      ['SQLite3', conf.sqlite3],
      ['SQLite', conf.sqliteLibversion],
      ['MySQLi client', conf.mysqliClientVersion],
      ['Mongo', conf.mongo],
      ['MongoDB', conf.mongoDb],
      ['PostgreSQL', conf.postgreSql],
      ['Paradox', conf.paradox],
      ['MS SQL', conf.msSql],
      ['File Pro', conf.filePro],
      ['MaxDB client', conf.maxDbClient],
      ['MaxDB server', conf.maxDbServer],
    ]

    return (
      <Row>
        {shortItems.map(([name, content]: [string, boolean | string]) => {
          return (
            <CardGrid
              key={name}
              title={name}
              mobileMd={[1, 2]}
              tablet={[1, 3]}
              desktopMd={[1, 4]}
              desktopLg={[1, 5]}
            >
              <Alert isSuccess={!!content} msg={content} />
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default Database
