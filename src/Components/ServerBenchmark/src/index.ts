import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'serverBechmark',
  title: gettext('Server Bechmark'),
  tinyTitle: gettext('Bechmark'),
  priority: 800,
  component,
})
