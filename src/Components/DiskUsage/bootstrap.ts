import { CardStore } from '../Card/stores'
import { gettext } from '../Language'
import { DiskUsage as component } from './components'
import { DiskUsageConstants } from './constants'
export const DiskUsageBootstrap = (): void => {
  const { id, isEnable } = DiskUsageConstants
  isEnable &&
    CardStore.addCard({
      id,
      title: gettext('Disk usage'),
      tinyTitle: gettext('Disk'),
      priority: 250,
      component,
    })
}
