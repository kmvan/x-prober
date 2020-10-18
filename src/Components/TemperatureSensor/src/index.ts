import CardStore from '@/Card/src/stores'
import { gettext } from '@/Language/src'
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
