import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'serverStatus',
  title: gettext('Server Status'),
  tinyTitle: gettext('Status'),
  priority: 100,
  component,
})
