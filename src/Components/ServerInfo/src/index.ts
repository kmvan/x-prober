import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'serverInfo',
  title: gettext('Server Information'),
  tinyTitle: gettext('Info'),
  priority: 300,
  component,
})
