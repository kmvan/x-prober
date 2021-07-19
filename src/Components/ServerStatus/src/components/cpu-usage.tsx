import { observer } from 'mobx-react-lite'
import React, { FC } from 'react'
import { CardGrid } from '../../../Card/src/components/card-grid'
import { gettext } from '../../../Language/src'
import { ProgressBar } from '../../../ProgressBar/src/components'
import { template } from '../../../Utils/src/components/template'
import { ServerStatusStore } from '../stores'

export const CpuUsage: FC = observer(() => {
  const { cpuUsage } = ServerStatusStore
  const { idle } = cpuUsage
  return (
    <CardGrid name={gettext('CPU usage')} tablet={[1, 1]}>
      <ProgressBar
        title={template(
          gettext(
            'idle: {{idle}} \nnice: {{nice}} \nsys: {{sys}} \nuser: {{user}}'
          ),
          cpuUsage as any
        )}
        value={100 - idle}
        max={100}
        isCapacity={false}
      />
    </CardGrid>
  )
})
