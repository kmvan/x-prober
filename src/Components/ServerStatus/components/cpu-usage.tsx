import { observer } from 'mobx-react-lite'
import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { gettext } from '../../Language'
import { ProgressBar } from '../../ProgressBar/components'
import { template } from '../../Utils/components/template'
import { ServerStatusStore } from '../stores'
export const CpuUsage: FC = observer(() => {
  const { cpuUsage } = ServerStatusStore
  const { idle } = cpuUsage
  return (
    <CardGrid name={gettext('CPU usage')}>
      <ProgressBar
        title={template(
          gettext(
            'idle: {{idle}} \nnice: {{nice}} \nsys: {{sys}} \nuser: {{user}}',
          ),
          cpuUsage as any,
        )}
        value={100 - idle}
        max={100}
        isCapacity={false}
      />
    </CardGrid>
  )
})
