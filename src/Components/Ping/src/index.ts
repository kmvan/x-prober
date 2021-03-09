import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { Ping as component } from './components'
import { PingStore } from './stores'
CardStore.addCard({
  id: PingStore.ID,
  title: gettext('Network Ping'),
  tinyTitle: gettext('Ping'),
  priority: 250,
  component,
})
