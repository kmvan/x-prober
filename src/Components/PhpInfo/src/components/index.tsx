import { CardGrid } from '@/Card/src/components/card-grid'
import { MultiItemContainer } from '@/Card/src/components/multi-item-container'
import { Row } from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import { Alert } from '@/Utils/src/components/alert'
import { SearchLink } from '@/Utils/src/components/search-link'
import { observer } from 'mobx-react-lite'
import React, { ReactNode } from 'react'
import { PhpInfoStore } from '../stores'
import { PhpInfoPhpVersion } from './php-version'
export const PhpInfo = observer(() => {
  const { conf } = PhpInfoStore
  const oneLineItems: Array<[string, ReactNode]> = [
    [
      'PHP info',
      <a key='phpInfoDetail' href='?action=phpInfoDetail' target='_blank'>
        {gettext('👆 Click for detail')}
      </a>,
    ],
    [gettext('Version'), <PhpInfoPhpVersion key='phpVersion' />],
  ]
  const shortItems = [
    [gettext('SAPI interface'), conf?.sapi],
    [
      gettext('Display errors'),
      <Alert key='displayErrors' isSuccess={conf?.displayErrors} />,
    ],
    [gettext('Error reporting'), conf?.errorReporting],
    [gettext('Max memory limit'), conf?.memoryLimit],
    [gettext('Max POST size'), conf?.postMaxSize],
    [gettext('Max upload size'), conf?.uploadMaxFilesize],
    [gettext('Max input variables'), conf?.maxInputVars],
    [gettext('Max execution time'), conf?.maxExecutionTime],
    [gettext('Timeout for socket'), conf?.defaultSocketTimeout],
    [
      gettext('Treatment URLs file'),
      <Alert key='allowUrlFopen' isSuccess={conf?.allowUrlFopen} />,
    ],
    [gettext('SMTP support'), <Alert key='smtp' isSuccess={conf?.smtp} />],
  ]
  const { disableFunctions, disableClasses } = conf
  disableFunctions.sort()
  disableClasses.sort()
  const longItems: Array<[string, ReactNode]> = [
    [
      gettext('Disabled functions'),
      disableFunctions.length
        ? disableFunctions.map((fn: string, i: number) => (
            <SearchLink key={i} keyword={fn} />
          ))
        : '-',
    ],
    [
      gettext('Disabled classes'),
      disableClasses.length
        ? disableClasses.map((fn: string, i: number) => (
            <SearchLink key={i} keyword={fn} />
          ))
        : '-',
    ],
  ]
  return (
    <Row>
      {oneLineItems.map(([title, content]) => {
        return (
          <CardGrid
            key={title}
            name={title}
            tablet={[1, 3]}
            desktopMd={[1, 4]}
            desktopLg={[1, 5]}>
            {content}
          </CardGrid>
        )
      })}
      {shortItems.map(([title, content]) => {
        return (
          <CardGrid
            key={title}
            name={title}
            mobileMd={[1, 2]}
            tablet={[1, 3]}
            desktopMd={[1, 4]}
            desktopLg={[1, 5]}>
            {content}
          </CardGrid>
        )
      })}
      {longItems.map(([title, content]) => {
        return (
          <CardGrid key={title} name={title}>
            <MultiItemContainer>{content}</MultiItemContainer>
          </CardGrid>
        )
      })}
    </Row>
  )
})
