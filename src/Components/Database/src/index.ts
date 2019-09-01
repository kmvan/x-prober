import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'database',
  title: gettext('Database'),
  tinyTitle: gettext('DB'),
  priority: 600,
  component,
})
