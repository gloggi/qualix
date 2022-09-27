import React from 'react'
import {Font, Document, Page, View, Text, Image} from '@react-pdf/renderer'
import RequirementProgress from './RequirementProgress.jsx'
import SourceSansPro from '../../../../fonts/SourceSansPro-Regular.ttf'
import SourceSansProItalic from '../../../../fonts/SourceSansPro-Italic.ttf'
import SourceSansProBold from '../../../../fonts/SourceSansPro-Bold.ttf'
import SourceSansProBoldItalic from '../../../../fonts/SourceSansPro-BoldItalic.ttf'
import FontAwesomeSolid from '@fortawesome/fontawesome-free/webfonts/fa-solid-900.ttf'
import wasGaffsch from '../../../../images/was-gaffsch.png'
import FeedbackContents from './FeedbackContents'
import styles from './styles.js'

function FeedbackDocument({ course, feedback, feedbackContents, participant, observations, statuses, t }) {
    const imageUrl = participant.image_path.match(/\.svg$/gi) ? wasGaffsch : participant.image_path
    return (
        <Document>
            <Page size="A4" style={{ ...styles.page, paddingLeft: feedback.requirements.length === 0 ? styles.page.padding : '22mm' }}>
                <View style={{ display: 'flex', flexDirection: 'row' }}>
                    <View style={{ width: '20mm', marginRight: '10mm' }}>
                        <Image src={imageUrl} />
                    </View>
                    <View style={{ flexGrow: 1 }}>
                        <Text style={styles.h2}>{feedback.name}: {participant.scout_name}</Text>
                        {!participant.group ? <View/> : <Text style={styles.h5}>{ participant.group }</Text>}
                    </View>
                    <View style={{ width: '40mm', marginLeft: '10mm' }}>
                        <Text style={{ fontWeight: 'bold', marginTop: '4mm' }}>{ course.name }</Text>
                        <Text>{ course.course_number }</Text>
                        {feedback.users.length === 0 ? <View/> : <View style={{ marginTop: '2.2mm' }}>
                            <Text>{ t('t.models.feedback.users') }:</Text>
                            <Text>{ feedback.users.map(user => user.name).join(', ') }</Text>
                        </View>}
                    </View>
                </View>
                {feedback.requirements.length === 0 ? <View/> : <View>
                    <Text style={{ ...styles.h5, marginTop: '2mm' }}>{ t('t.views.feedback_content.requirements_status') }</Text>
                    <RequirementProgress requirements={feedback.requirements} statuses={statuses} />
                </View>}
                <FeedbackContents feedbackContents={feedbackContents} observations={observations} requirements={feedback.requirements} statuses={statuses} />
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

FeedbackDocument.prepare = async () => {
    return await registerFonts()
}

export default FeedbackDocument
