import React from 'react'
import {Font, Document, Page, View, Text, Image} from '@react-pdf/renderer'
import SourceSansPro from '../../../../fonts/SourceSansPro-Regular.ttf'
import SourceSansProItalic from '../../../../fonts/SourceSansPro-Italic.ttf'
import SourceSansProBold from '../../../../fonts/SourceSansPro-Bold.ttf'
import SourceSansProBoldItalic from '../../../../fonts/SourceSansPro-BoldItalic.ttf'
import FontAwesomeSolid from '@fortawesome/fontawesome-free/webfonts/fa-solid-900.ttf'
import styles from './styles.js'

function EvaluationGridDocument({ course, evaluationGridTemplate, evaluationGrid, participants, block, user, t }) {
    console.log(user)
    return (
        <Document>
            <Page size="A4" style={ styles.page }>
                <Text
                    style={styles.pageNumbering}
                    render={({ subPageNumber, subPageTotalPages }) => `${subPageNumber} / ${subPageTotalPages}`}
                    fixed
                />
                <View style={{ display: 'flex', flexDirection: 'row' }}>
                    <View style={{ flexGrow: 1, display: 'flex', flexDirection: 'column', width: '100%' }}>
                        <Text style={styles.h2}>{ evaluationGridTemplate.name }</Text>
                        { participants.length ?
                            <Text style={styles.h5}>{participants.map(participant => participant.scout_name + (!participant.group ? '' : ` (${participant.group})`)).join(', ')}</Text> :
                            <View style={{ display: 'flex', flexDirection: 'row' }}>
                                <Text style={styles.h5}>{t('t.models.evaluation_grid.participants')}:</Text>
                                <View style={{ ...styles.emptyLine, marginLeft: '3mm' }} />
                            </View>
                        }
                        { block ?
                            <Text style={styles.h5}>{block.blockname_and_number}</Text> :
                            <View style={{ display: 'flex', flexDirection: 'row' }}>
                                <Text style={styles.h5}>{t('t.models.evaluation_grid.block')}:</Text>
                                <View style={{ ...styles.emptyLine, marginLeft: '3mm' }} />
                            </View>
                        }
                    </View>
                    <View style={{ flexGrow: 0, width: '60mm', marginLeft: '10mm' }}>
                        <Text style={{ fontWeight: 'bold', marginTop: '1mm' }}>{ course.name }</Text>
                        <Text>{ course.course_number }</Text>
                        <View style={{ marginTop: '2.2mm' }}>
                            <Text>{ t('t.models.evaluation_grid.user') }:</Text>
                            {user ? <Text>{ user.name }</Text> : <View style={{ ...styles.emptyLine, marginTop: '2mm' }} /> }
                        </View>
                    </View>
                </View>
                <Text style={{ ...styles.h5, marginTop: '4mm' }}>{ t('t.models.evaluation_grid_template.requirements') }:</Text>
                { evaluationGridTemplate.requirements.map(requirement => <View><Text>• { requirement.content }</Text></View>) }
                {/*evaluationGrid.rows.map(row => <EvaluationGridRow row={row} t={t} />)*/}
            </Page>
        </Document>
    )
}

const registerFonts = async () => {
    Font.register({
        family: 'SourceSansPro',
        fonts: [
            {src: SourceSansPro},
            {src: SourceSansProBold, fontWeight: 'bold'},
            {src: SourceSansProItalic, fontStyle: 'italic'},
            {src: SourceSansProBoldItalic, fontWeight: 'bold', fontStyle: 'italic'},
        ],
    })
    Font.register({
        family: 'FontAwesome',
        fonts: [
            {src: FontAwesomeSolid},
        ],
    })

    return await Promise.all([
        Font.load({fontFamily: 'SourceSansPro'}),
        Font.load({fontFamily: 'SourceSansPro', fontWeight: 700}),
        Font.load({fontFamily: 'SourceSansPro', fontStyle: 'italic'}),
        Font.load({fontFamily: 'SourceSansPro', fontWeight: 700, fontStyle: 'italic'}),
        Font.load({fontFamily: 'FontAwesome'}),
    ])
}

EvaluationGridDocument.prepare = async () => {
    return await registerFonts()
}

export default EvaluationGridDocument
