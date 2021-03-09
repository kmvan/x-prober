import { CardStore } from '@/Card/src/stores'
import { gettext } from '@/Language/src'
import { NetworkStats as component } from './components'
import { NetworkStatsStore } from './stores'
NetworkStatsStore.enabled &&
  CardStore.addCard({
    id: NetworkStatsStore.ID,
    title: gettext('Network Stats'),
    tinyTitle: gettext('Net'),
    priority: 200,
    component,
  })
