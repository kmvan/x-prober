import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { GridContainer } from '../../Grid/components/container'
import { Alert } from '../../Utils/components/alert'
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
    ['PDO', conf?.pdo],
  ]
  return (
    <GridContainer>
      {shortItems.map(([name, content]) => (
        <CardGrid key={name} name={name} sm={2} lg={2} xl={3} xxl={4}>
          <Alert isSuccess={Boolean(content)} msg={content} />
        </CardGrid>
      ))}
    </GridContainer>
  )
})
