import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { TemperatureSensor as component } from './components'
import { TemperatureSensorStore } from './stores'
CardStore.addCard({
  id: TemperatureSensorStore.ID,
  title: gettext('Temperature Sensor'),
  tinyTitle: gettext('Temp.'),
  enabled: false,
  priority: 240,
  component,
})
