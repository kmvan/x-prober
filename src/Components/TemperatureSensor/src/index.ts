import CardStore from '@/Card/src/stores'
import component from './components'
import store from './stores'
import { gettext } from '@/Language/src'
CardStore.addCard({
  id: store.ID,
  title: gettext('Temperature Sensor'),
  tinyTitle: gettext('Temp.'),
  enabled: false,
  priority: 240,
  component,
})
