import CardStore from '~components/Card/src/stores'
import { gettext } from '~components/Language/src'
import component from './components'
import store from './stores'

CardStore.addCard({
  id: store.ID,
  title: gettext('Temperature Sensor'),
  tinyTitle: gettext('Temp.'),
  enabled: false,
  priority: 240,
  component,
})
