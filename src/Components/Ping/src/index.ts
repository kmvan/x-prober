import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'

CardStore.addCard({
  id: 'ping',
  title: gettext('Network Ping'),
  tinyTitle: gettext('Ping'),
  priority: 250,
  component,
})
