import React, { Component } from 'react'
import { observer } from 'mobx-react'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store from '../stores'
import Alert from '~components/Helper/src/components/alert'

@observer
class Database extends Component {
  public render() {
    const { conf } = store
    const shortItems: any[] = [
      [gettext('SQLite3'), conf.sqlite3],
      [gettext('SQLite'), conf.sqliteLibversion],
      [gettext('MySQLi client'), conf.mysqliClientVersion],
      [gettext('Mongo'), conf.mongo],
      [gettext('MongoDB'), conf.mongoDb],
      [gettext('PostgreSQL'), conf.postgreSql],
      [gettext('Paradox'), conf.paradox],
      [gettext('MS SQL'), conf.msSql],
      [gettext('File Pro'), conf.filePro],
      [gettext('MaxDB client'), conf.maxDbClient],
      [gettext('MaxDB server'), conf.maxDbServer],
    ]

    return (
      <Row>
        {shortItems.map(([name, enabled]: [string, boolean | string]) => {
          return (
            <CardGrid key={name} title={name} tablet={[1, 3]}>
              {typeof enabled !== 'boolean' ? (
                <Alert isSuccess={true} msg={enabled} />
              ) : (
                <Alert isSuccess={enabled} />
              )}
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default Database
